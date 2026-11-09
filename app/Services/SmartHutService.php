<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Forestry figures for the public pages, read from SMART-HUT (Sistem Informasi Data Kehutanan).
 *
 * The CDK summary comes from SMART-HUT's public endpoint (see docs/smart-hut-integration.md) and is
 * cached. When SMART-HUT is not configured or cannot be reached, the last good response is used, and
 * failing that the snapshot bundled in resources/data/smart-hut/ringkasan.json.
 */
class SmartHutService
{
    /** Regions shown on the site, keyed by slug, with the SMART-HUT regency ids each one covers. */
    public const REGIONS = [
        'trenggalek' => ['name' => 'Trenggalek', 'label' => 'Kabupaten Trenggalek', 'regencies' => ['3503']],
        'tulungagung' => ['name' => 'Tulungagung', 'label' => 'Kabupaten Tulungagung', 'regencies' => ['3504']],
        'kediri' => ['name' => 'Kediri', 'label' => 'Kabupaten & Kota Kediri', 'regencies' => ['3506', '3571']],
    ];

    /** Per-regency figures in the SMART-HUT response that are summed into regions and totals. */
    public const REGION_METRICS = ['kth', 'anggota_kth', 'ps', 'ps_kk', 'nilai_transaksi_ekonomi'];

    private const CACHE_KEY = 'smart_hut.ringkasan';

    private const LAST_GOOD_KEY = 'smart_hut.ringkasan.last_good';

    /**
     * The CDK summary grouped by region, with totals, yearly achievements and where it came from.
     */
    public function summary(): array
    {
        $raw = Cache::remember(self::CACHE_KEY, config('services.smart_hut.cache_ttl'), fn () => $this->fetch());

        return $this->normalize($raw);
    }

    private function fetch(): array
    {
        $url = config('services.smart_hut.url');

        if ($url) {
            try {
                $token = config('services.smart_hut.token');
                $data = Http::baseUrl($url)
                    ->when($token, fn ($request) => $request->withToken($token))
                    ->acceptJson()
                    ->timeout(config('services.smart_hut.timeout'))
                    ->get('api/public/cdk/' . config('services.smart_hut.cdk_id') . '/ringkasan')
                    ->throw()
                    ->json();

                if (is_array($data) && isset($data['wilayah'])) {
                    $data['sumber'] = 'smart-hut';
                    Cache::forever(self::LAST_GOOD_KEY, $data);

                    return $data;
                }

                Log::warning('SMART-HUT summary has an unexpected shape.');
            } catch (Throwable $e) {
                Log::warning('SMART-HUT summary unavailable: ' . $e->getMessage());
            }
        }

        return Cache::get(self::LAST_GOOD_KEY) ?? $this->snapshot();
    }

    private function snapshot(): array
    {
        $data = json_decode(file_get_contents(resource_path('data/smart-hut/ringkasan.json')), true);

        return ['sumber' => 'snapshot'] + $data;
    }

    private function normalize(array $raw): array
    {
        $byRegency = collect($raw['wilayah'] ?? [])->keyBy(fn (array $row) => (string) $row['regency_id']);

        $regions = [];
        foreach (self::REGIONS as $slug => $region) {
            $rows = $byRegency->only($region['regencies']);
            $regions[$slug] = ['slug' => $slug, 'name' => $region['name'], 'label' => $region['label']];
            foreach (self::REGION_METRICS as $metric) {
                $regions[$slug][$metric] = $rows->sum(fn (array $row) => $row[$metric] ?? 0);
            }
        }

        $totals = [];
        foreach (self::REGION_METRICS as $metric) {
            $totals[$metric] = array_sum(array_column($regions, $metric));
        }

        $capaian = $raw['capaian'] ?? [];

        return [
            'sumber' => $raw['sumber'] ?? 'snapshot',
            'diperbarui' => isset($raw['diperbarui']) ? Carbon::parse($raw['diperbarui']) : null,
            'tahun' => (int) ($raw['tahun'] ?? now()->year),
            'regions' => $regions,
            'totals' => $totals,
            'capaian' => [
                'rehab_lahan' => ($capaian['rehab_lahan'] ?? []) + ['realisasi' => 0, 'target' => 0],
                'penghijauan' => ($capaian['penghijauan'] ?? []) + ['realisasi' => 0, 'target' => 0],
                'wisata' => ($capaian['wisata'] ?? []) + ['pengunjung' => 0, 'pendapatan' => 0],
                'kebakaran' => ($capaian['kebakaran'] ?? []) + ['kejadian' => 0, 'luas' => 0],
            ],
        ];
    }
}

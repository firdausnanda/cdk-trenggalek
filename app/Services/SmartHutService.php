<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Forestry figures for the public pages, read from SMART-HUT (Sistem Informasi Data Kehutanan).
 *
 * The figures come from SMART-HUT's summary endpoints /api/v1/{modul}/rekap, which count final data only,
 * scoped to the token's CDK (see docs/smart-hut-integration.md). Pages never call SMART-HUT: the scheduled
 * `smart-hut:sync` command stores the responses, and pages read the stored copy, or the snapshot bundled
 * in resources/data/smart-hut/rekap.json until the first sync succeeds.
 */
class SmartHutService
{
    /** Regions shown on the site, keyed by slug, with the SMART-HUT regency ids each one covers. */
    public const REGIONS = [
        'trenggalek' => ['name' => 'Trenggalek', 'label' => 'Kabupaten Trenggalek', 'regencies' => ['3503']],
        'tulungagung' => ['name' => 'Tulungagung', 'label' => 'Kabupaten Tulungagung', 'regencies' => ['3504']],
        'kediri' => ['name' => 'Kediri', 'label' => 'Kabupaten & Kota Kediri', 'regencies' => ['3506', '3571']],
    ];

    /** Per-region figures, summed into regions. */
    public const REGION_METRICS = ['kth', 'anggota_kth', 'ps', 'ps_kk', 'nilai_transaksi_ekonomi'];

    /** SMART-HUT summary endpoints, by module. */
    public const MODULES = ['pemberdayaan', 'rhl', 'perlindungan'];

    public const CACHE_KEY = 'smart_hut.rekap';

    /**
     * The CDK summary grouped by region, with totals, yearly achievements and where it came from.
     */
    public function summary(): array
    {
        return $this->normalize(Cache::get(self::CACHE_KEY) ?? $this->snapshot());
    }

    /**
     * Fetch the summaries from SMART-HUT and store them for the pages. On failure the stored copy is kept.
     *
     * @throws Throwable when SMART-HUT is not configured or cannot be read
     */
    public function sync(): array
    {
        if (! config('services.smart_hut.url')) {
            throw new \RuntimeException('SMART_HUT_URL is not set.');
        }

        $data = ['sumber' => 'smart-hut'];
        foreach (self::MODULES as $module) {
            $data[$module] = $this->rekap($module);
        }

        Cache::forever(self::CACHE_KEY, $data);

        return $this->normalize($data);
    }

    private function rekap(string $module): array
    {
        $token = config('services.smart_hut.token');
        $response = Http::baseUrl(rtrim(config('services.smart_hut.url'), '/') . '/api/v1/')
            ->when($token, fn ($request) => $request->withToken($token))
            ->acceptJson()
            ->timeout(config('services.smart_hut.timeout'))
            ->retry(3, 2000, fn (Throwable $e) => $e instanceof ConnectionException
                || ($e instanceof RequestException && ($e->response->serverError() || $e->response->status() === 429)))
            ->get("{$module}/rekap")
            ->json();

        if (! is_array($response['data'] ?? null) || ! array_key_exists('tahun', $response['data'])) {
            throw new \UnexpectedValueException("SMART-HUT {$module}/rekap has an unexpected shape.");
        }

        return $response['data'];
    }

    private function snapshot(): array
    {
        $data = json_decode(file_get_contents(resource_path('data/smart-hut/rekap.json')), true);

        return ['sumber' => 'snapshot'] + $data;
    }

    private function normalize(array $raw): array
    {
        $pemberdayaan = $raw['pemberdayaan'] ?? [];
        $rhl = $raw['rhl'] ?? [];
        $perlindungan = $raw['perlindungan'] ?? [];

        $byRegency = collect($pemberdayaan['per_kabupaten'] ?? [])
            ->keyBy(fn (array $row) => (string) $row['kabupaten']['id'])
            ->map(fn (array $row) => [
                'kth' => $row['kth']['jumlah'] ?? 0,
                'anggota_kth' => $row['kth']['jumlah_anggota'] ?? 0,
                'ps' => $row['perhutanan_sosial']['jumlah_kelompok'] ?? 0,
                'ps_kk' => $row['perhutanan_sosial']['jumlah_kk'] ?? 0,
                'nilai_transaksi_ekonomi' => $row['nilai_transaksi_ekonomi_rp'] ?? 0,
            ]);

        $regions = [];
        foreach (self::REGIONS as $slug => $region) {
            $rows = $byRegency->only($region['regencies']);
            $regions[$slug] = ['slug' => $slug, 'name' => $region['name'], 'label' => $region['label']];
            foreach (self::REGION_METRICS as $metric) {
                $regions[$slug][$metric] = $rows->sum($metric);
            }
        }

        $transaksi = $pemberdayaan['nilai_transaksi_ekonomi'] ?? [];
        $rehab = $rhl['rehab_lahan'] ?? [];
        $hijau = $rhl['penghijauan_lingkungan'] ?? [];
        $wisata = $perlindungan['pengunjung_wisata'] ?? [];
        $api = $perlindungan['kebakaran_hutan'] ?? [];

        $updated = collect([$pemberdayaan, $rhl, $perlindungan])->pluck('diperbarui_pada')->filter()
            ->map(fn (string $at) => Carbon::parse($at))->max();

        return [
            'sumber' => $raw['sumber'] ?? 'snapshot',
            'diperbarui' => $updated,
            'tahun' => $pemberdayaan['tahun'] ?? null,
            'periode' => $transaksi['periode'] ?? null,
            'regions' => $regions,
            'totals' => [
                'kth' => $pemberdayaan['kth']['jumlah'] ?? 0,
                'anggota_kth' => $pemberdayaan['kth']['jumlah_anggota'] ?? 0,
                'ps' => $pemberdayaan['perhutanan_sosial']['jumlah_kelompok'] ?? 0,
                'ps_kk' => $pemberdayaan['perhutanan_sosial']['jumlah_kk'] ?? 0,
                'nilai_transaksi_ekonomi' => $transaksi['total_rp'] ?? 0,
            ],
            'capaian' => [
                'tahun' => $rhl['tahun'] ?? $perlindungan['tahun'] ?? null,
                'rehab_lahan' => [
                    'tahun' => $rhl['tahun'] ?? null,
                    'periode' => $rehab['periode'] ?? null,
                    'realisasi' => $rehab['realisasi_ha'] ?? 0,
                    'target' => $rehab['target_tahunan_ha'] ?? 0,
                    'persen' => $rehab['persentase_realisasi'] ?? null,
                ],
                'penghijauan' => [
                    'tahun' => $rhl['tahun'] ?? null,
                    'periode' => $hijau['periode'] ?? null,
                    'realisasi' => $hijau['realisasi_ha'] ?? 0,
                    'target' => $hijau['target_tahunan_ha'] ?? 0,
                    'persen' => $hijau['persentase_realisasi'] ?? null,
                ],
                'wisata' => [
                    'tahun' => $perlindungan['tahun'] ?? null,
                    'periode' => $wisata['periode'] ?? null,
                    'pengunjung' => $wisata['jumlah_pengunjung'] ?? 0,
                    'pendapatan' => $wisata['pendapatan_bruto_rp'] ?? 0,
                    'pengelola' => array_values(array_filter(array_map(
                        fn (array $row) => $row['pengelola']['nama'] ?? null,
                        $wisata['per_pengelola'] ?? [],
                    ))),
                ],
                'kebakaran' => [
                    'tahun' => $perlindungan['tahun'] ?? null,
                    'periode' => $api['periode'] ?? null,
                    'kejadian' => $api['jumlah_kejadian'] ?? 0,
                    'luas' => $api['luas_terbakar_ha'] ?? 0,
                ],
            ],
        ];
    }

    /**
     * "Jan–Sep 2026", "Agt 2026", or just the year for a full or unknown month range.
     */
    public static function periodLabel(?array $periode, ?int $tahun): string
    {
        if (! $tahun) {
            return '';
        }

        $awal = (int) ($periode['bulan_awal'] ?? 1);
        $akhir = (int) ($periode['bulan_akhir'] ?? 12);
        if (! $periode || ($awal === 1 && $akhir === 12)) {
            return (string) $tahun;
        }

        $month = fn (int $m) => Carbon::create($tahun, $m, 1)->translatedFormat('M');

        return ($awal === $akhir ? $month($awal) : $month($awal) . '–' . $month($akhir)) . ' ' . $tahun;
    }
}

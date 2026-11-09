<?php

namespace Tests\Feature;

use App\Services\SmartHutService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SmartHutServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        config(['services.smart_hut.url' => 'https://smarthut.test', 'services.smart_hut.cdk_id' => 3]);
    }

    private function response(array $overrides = []): array
    {
        $row = fn (string $id, int $kth) => [
            'regency_id' => $id, 'kth' => $kth, 'anggota_kth' => $kth * 10, 'ps' => 1, 'ps_kk' => 2, 'nilai_transaksi_ekonomi' => 1.5,
        ];

        return $overrides + [
            'tahun' => 2026,
            'diperbarui' => '2026-09-10T01:34:22+07:00',
            'wilayah' => [$row('3503', 10), $row('3504', 20), $row('3506', 30), $row('3571', 4)],
            'capaian' => ['rehab_lahan' => ['realisasi' => 5, 'target' => 10]],
        ];
    }

    #[Test]
    public function it_groups_regencies_into_site_regions(): void
    {
        Http::fake(['smarthut.test/api/public/cdk/3/ringkasan' => Http::response($this->response())]);

        $summary = app(SmartHutService::class)->summary();

        $this->assertSame('smart-hut', $summary['sumber']);
        $this->assertSame(['trenggalek', 'tulungagung', 'kediri'], array_keys($summary['regions']));
        $this->assertEquals(34, $summary['regions']['kediri']['kth']); // Kabupaten + Kota Kediri
        $this->assertEquals(64, $summary['totals']['kth']);
        $this->assertEquals(6.0, $summary['totals']['nilai_transaksi_ekonomi']);
        $this->assertSame(['realisasi' => 5, 'target' => 10], $summary['capaian']['rehab_lahan']);
        $this->assertSame(['pengunjung' => 0, 'pendapatan' => 0], $summary['capaian']['wisata']);
    }

    #[Test]
    public function it_caches_the_response(): void
    {
        Http::fake(['*' => Http::response($this->response())]);

        app(SmartHutService::class)->summary();
        app(SmartHutService::class)->summary();

        Http::assertSentCount(1);
    }

    #[Test]
    public function it_falls_back_to_the_last_good_response_when_smart_hut_fails(): void
    {
        Http::fakeSequence()
            ->push($this->response(['tahun' => 2025]))
            ->pushStatus(500);

        app(SmartHutService::class)->summary();
        Cache::forget('smart_hut.ringkasan');
        $summary = app(SmartHutService::class)->summary();

        $this->assertSame(2025, $summary['tahun']);
        $this->assertSame('smart-hut', $summary['sumber']);
    }

    #[Test]
    public function it_uses_the_bundled_snapshot_when_smart_hut_is_not_configured(): void
    {
        config(['services.smart_hut.url' => null]);
        Http::fake();

        $summary = app(SmartHutService::class)->summary();

        $this->assertSame('snapshot', $summary['sumber']);
        $this->assertGreaterThan(0, $summary['totals']['kth']);
        Http::assertNothingSent();
    }
}

<?php

namespace Tests\Feature;

use App\Services\SmartHutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SmartHutServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        config(['services.smart_hut.url' => 'https://smarthut.test', 'services.smart_hut.token' => 'test-token']);
    }

    private function kabupaten(string $id, int $kth, int $ps, float $nilai): array
    {
        return [
            'kabupaten' => ['id' => $id, 'nama' => "KAB $id"],
            'kth' => ['jumlah' => $kth, 'jumlah_anggota' => $kth * 10, 'luas_kelola_ha' => 0],
            'perhutanan_sosial' => ['jumlah_kelompok' => $ps, 'jumlah_kk' => $ps * 100, 'luas_ps_ha' => 1],
            'nilai_transaksi_ekonomi_rp' => $nilai,
        ];
    }

    private function fakeApi(): void
    {
        $periode = fn (int $awal, int $akhir) => ['bulan_awal' => $awal, 'bulan_akhir' => $akhir];

        Http::fake([
            'smarthut.test/api/v1/pemberdayaan/rekap' => Http::response(['data' => [
                'tahun' => 2026,
                'kth' => ['jumlah' => 7, 'jumlah_anggota' => 70, 'luas_kelola_ha' => 0, 'jumlah_catatan' => 9],
                'perhutanan_sosial' => ['jumlah_kelompok' => 4, 'jumlah_kk' => 400, 'luas_ps_ha' => 4],
                'nilai_transaksi_ekonomi' => ['total_rp' => 6.5, 'jumlah_catatan' => 3, 'periode' => $periode(1, 9), 'per_bulan' => []],
                'per_kabupaten' => [
                    $this->kabupaten('3503', 1, 1, 1.5),
                    $this->kabupaten('3504', 2, 1, 2),
                    $this->kabupaten('3506', 3, 1, 2),
                    $this->kabupaten('3571', 1, 1, 1),
                ],
                'diperbarui_pada' => '2026-10-23T07:27:06+00:00',
            ]]),
            'smarthut.test/api/v1/rhl/rekap' => Http::response(['data' => [
                'tahun' => 2026,
                'rehab_lahan' => ['target_tahunan_ha' => 49, 'realisasi_ha' => 10, 'persentase_realisasi' => 20.4, 'periode' => $periode(2, 11)],
                'penghijauan_lingkungan' => ['target_tahunan_ha' => 0, 'realisasi_ha' => 35, 'persentase_realisasi' => null, 'periode' => $periode(1, 4)],
                'diperbarui_pada' => '2026-09-25T01:58:16+00:00',
            ]]),
            'smarthut.test/api/v1/perlindungan/rekap' => Http::response(['data' => [
                'tahun' => 2026,
                'kebakaran_hutan' => ['jumlah_kejadian' => 26, 'luas_terbakar_ha' => 47.1, 'periode' => $periode(6, 9)],
                'pengunjung_wisata' => [
                    'jumlah_pengunjung' => 100, 'pendapatan_bruto_rp' => 2000, 'periode' => $periode(1, 8),
                    'per_pengelola' => [['pengelola' => ['id' => 9, 'nama' => 'Rimba Madu Sejahtera'], 'jumlah_pengunjung' => 100]],
                ],
                'diperbarui_pada' => '2026-10-23T07:23:35+00:00',
            ]]),
        ]);
    }

    #[Test]
    public function it_maps_the_rekap_endpoints_into_site_regions(): void
    {
        $this->fakeApi();

        app(SmartHutService::class)->sync();
        $summary = app(SmartHutService::class)->summary();

        $this->assertSame('smart-hut', $summary['sumber']);
        $this->assertSame(['trenggalek', 'tulungagung', 'kediri'], array_keys($summary['regions']));
        $this->assertEquals(4, $summary['regions']['kediri']['kth']); // Kabupaten + Kota Kediri
        $this->assertEquals(3.0, $summary['regions']['kediri']['nilai_transaksi_ekonomi']);
        $this->assertSame(7, $summary['totals']['kth']); // deduplicated by SMART-HUT, not summed from rows
        $this->assertSame(400, $summary['totals']['ps_kk']);
        $this->assertSame(['bulan_awal' => 1, 'bulan_akhir' => 9], $summary['periode']);
        $this->assertEquals(20.4, $summary['capaian']['rehab_lahan']['persen']);
        $this->assertNull($summary['capaian']['penghijauan']['persen']);
        $this->assertSame(['Rimba Madu Sejahtera'], $summary['capaian']['wisata']['pengelola']);
        $this->assertSame(2026, $summary['capaian']['tahun']);
        $this->assertSame('2026-10-23 07:27:06', $summary['diperbarui']->utc()->toDateTimeString());

        Http::assertSentCount(3);
        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer test-token'));
    }

    #[Test]
    public function it_labels_the_period_of_the_data(): void
    {
        app()->setLocale('id');

        $this->assertSame('Jan–Sep 2026', SmartHutService::periodLabel(['bulan_awal' => 1, 'bulan_akhir' => 9], 2026));
        $this->assertSame('Agt 2026', SmartHutService::periodLabel(['bulan_awal' => 8, 'bulan_akhir' => 8], 2026));
        $this->assertSame('2025', SmartHutService::periodLabel(['bulan_awal' => 1, 'bulan_akhir' => 12], 2025));
        $this->assertSame('2025', SmartHutService::periodLabel(null, 2025));
        $this->assertSame('', SmartHutService::periodLabel(null, null));
    }

    #[Test]
    public function pages_never_call_smart_hut(): void
    {
        Http::fake();

        $summary = app(SmartHutService::class)->summary();

        $this->assertSame('snapshot', $summary['sumber']);
        Http::assertNothingSent();
    }

    #[Test]
    public function a_failed_sync_keeps_the_stored_summary(): void
    {
        Cache::forever(SmartHutService::CACHE_KEY, ['sumber' => 'smart-hut', 'pemberdayaan' => ['tahun' => 2025]]);
        Http::fake(['*' => Http::response(['message' => 'Unauthenticated.'], 401)]);

        $this->artisan('smart-hut:sync')->assertFailed();

        $summary = app(SmartHutService::class)->summary();
        $this->assertSame(2025, $summary['tahun']);
        $this->assertSame('smart-hut', $summary['sumber']);
        Http::assertSentCount(1); // 401 is not retried
    }

    #[Test]
    public function the_sync_command_stores_the_summary(): void
    {
        $this->fakeApi();

        $this->artisan('smart-hut:sync')->assertSuccessful();

        $this->assertSame('smart-hut', app(SmartHutService::class)->summary()['sumber']);
    }

    #[Test]
    public function it_uses_the_bundled_snapshot_when_smart_hut_is_not_configured(): void
    {
        config(['services.smart_hut.url' => null]);
        Http::fake();

        $this->artisan('smart-hut:sync')->assertSuccessful();
        $summary = app(SmartHutService::class)->summary();

        $this->assertSame('snapshot', $summary['sumber']);
        $this->assertGreaterThan(0, $summary['totals']['kth']);
        $this->assertGreaterThan(0, $summary['regions']['trenggalek']['kth']);
        Http::assertNothingSent();
    }

    #[Test]
    public function the_homepage_shows_the_period_and_tourism_reporters(): void
    {
        app()->setLocale('id');
        $this->fakeApi();
        app(SmartHutService::class)->sync();

        $this->get('/')
            ->assertOk()
            ->assertSee('Nilai transaksi ekonomi Jan–Sep 2026')
            ->assertSee('dilaporkan 1 pengelola: Rimba Madu Sejahtera')
            ->assertSee('20% dari target 49,0 ha');
    }
}

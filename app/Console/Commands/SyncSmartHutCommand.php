<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\SmartHutService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncSmartHutCommand extends Command
{
    protected $signature = 'smart-hut:sync';

    protected $description = 'Ambil ringkasan data kehutanan dari SMART-HUT untuk beranda';

    public function handle(SmartHutService $smartHut): int
    {
        if (! config('services.smart_hut.url')) {
            $this->warn('SMART_HUT_URL kosong, beranda memakai snapshot bawaan.');

            return self::SUCCESS;
        }

        try {
            $summary = $smartHut->sync();
        } catch (Throwable $e) {
            Log::warning('SMART-HUT sync failed: ' . $e->getMessage());
            $this->error('Gagal mengambil data SMART-HUT: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Data SMART-HUT tersimpan: %d KTH, %d kelompok PS, diperbarui %s.',
            $summary['totals']['kth'],
            $summary['totals']['ps'],
            $summary['diperbarui']?->toDateTimeString() ?? '-',
        ));

        return self::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ReplaceDomainUrls extends Command
{
    /**
     * @var string
     */
    protected $signature = 'urls:replace-domain
                            {old : Old base URL, e.g. http://103.183.92.79:8081}
                            {new? : New base URL (defaults to APP_URL)}
                            {--dry-run : Only report what would change}
                            {--restore= : Restore rows from a backup file}';

    /**
     * @var string
     */
    protected $description = 'Replace an old domain in stored URLs (posts, settings, terms) with a new one';

    /**
     * Tables and text columns that may contain absolute URLs, keyed by table => [primary key, columns].
     *
     * @var array<string, array{0: string, 1: string[]}>
     */
    private const TARGETS = [
        'posts' => ['id', ['content', 'excerpt', 'featured_image', 'meta']],
        'settings' => ['id', ['option_value']],
        'terms' => ['id', ['featured_image', 'description']],
    ];

    public function handle(): int
    {
        if ($this->option('restore')) {
            return $this->restore((string) $this->option('restore'));
        }

        $old = rtrim((string) $this->argument('old'), '/');
        $new = rtrim((string) ($this->argument('new') ?: config('app.url')), '/');
        $dryRun = (bool) $this->option('dry-run');

        if ($old === '' || $new === '' || $old === $new) {
            $this->error('Old and new URLs must be non-empty and different.');

            return self::FAILURE;
        }

        // Plain form plus the JSON-escaped form ("http:\/\/host") used inside json columns.
        $pairs = [
            $old => $new,
            str_replace('/', '\/', $old) => str_replace('/', '\/', $new),
        ];

        $this->info(($dryRun ? '[DRY RUN] ' : '')."Replacing {$old} → {$new}");

        $backup = [];
        $summary = [];

        foreach (self::TARGETS as $table => [$key, $columns]) {
            if (! DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }

            $columns = array_values(array_filter($columns, fn ($c) => DB::getSchemaBuilder()->hasColumn($table, $c)));
            if ($columns === []) {
                continue;
            }

            $rows = DB::table($table)
                ->select(array_merge([$key], $columns))
                ->where(function ($query) use ($columns, $pairs) {
                    foreach ($columns as $column) {
                        foreach (array_keys($pairs) as $needle) {
                            $query->orWhere($column, 'like', '%'.addcslashes($needle, '%_\\').'%');
                        }
                    }
                })
                ->orderBy($key)
                ->get();

            foreach ($rows as $row) {
                $changes = [];
                $original = [];

                foreach ($columns as $column) {
                    $value = $row->{$column};
                    if ($value === null) {
                        continue;
                    }

                    $count = 0;
                    $replaced = str_replace(array_keys($pairs), array_values($pairs), (string) $value, $count);
                    if ($count > 0) {
                        $changes[$column] = $replaced;
                        $original[$column] = $value;
                        $summary[] = [$table, $row->{$key}, $column, $count];
                    }
                }

                if ($changes === []) {
                    continue;
                }

                $backup[] = ['table' => $table, 'key' => $key, 'id' => $row->{$key}, 'values' => $original];

                if (! $dryRun) {
                    DB::table($table)->where($key, $row->{$key})->update($changes);
                }
            }
        }

        if ($summary === []) {
            $this->info('Nothing to replace.');

            return self::SUCCESS;
        }

        $this->table(['Table', 'ID', 'Column', 'Occurrences'], $summary);

        if ($dryRun) {
            $this->warn(count($summary).' column value(s) would be updated. Run without --dry-run to apply.');

            return self::SUCCESS;
        }

        $file = $this->writeBackup($backup);
        $this->info(count($summary)." column value(s) updated. Backup: {$file}");

        Artisan::call('cache:clear');
        $this->info('Application cache cleared.');

        return self::SUCCESS;
    }

    /**
     * @param  array<int, array<string, mixed>>  $backup
     */
    private function writeBackup(array $backup): string
    {
        $directory = storage_path('backups');
        File::ensureDirectoryExists($directory);

        $file = $directory.'/replace-domain-'.now()->format('Ymd_His').'.json';
        File::put($file, json_encode($backup, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return $file;
    }

    private function restore(string $file): int
    {
        if (! File::exists($file)) {
            $this->error("Backup file not found: {$file}");

            return self::FAILURE;
        }

        $rows = json_decode((string) File::get($file), true) ?: [];

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                DB::table($row['table'])->where($row['key'], $row['id'])->update($row['values']);
            }
        });

        Artisan::call('cache:clear');
        $this->info(count($rows).' row(s) restored from backup.');

        return self::SUCCESS;
    }
}

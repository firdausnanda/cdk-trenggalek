<?php

namespace App\Console\Commands;

use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

class OptimizePostImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:optimize-images
                            {--dry-run : Only report what would change}
                            {--featured : Also convert local featured images to WebP with size variants}
                            {--restore= : Restore content and featured images from a backup file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move base64 images out of post content into optimized WebP files';

    public function __construct(private readonly ImageService $imageService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('restore')) {
            return $this->restore((string) $this->option('restore'));
        }

        $dryRun = (bool) $this->option('dry-run');
        $withFeatured = (bool) $this->option('featured');

        $posts = DB::table('posts')
            ->select('id', 'title', 'content', 'featured_image')
            ->where(function ($query) use ($withFeatured) {
                $query->where('content', 'like', '%data:image/%');
                if ($withFeatured) {
                    $query->orWhere(function ($query) {
                        $query->whereNotNull('featured_image')->where('featured_image', 'not like', '%.webp');
                    });
                }
            })
            ->orderBy('id')
            ->get();

        if ($posts->isEmpty()) {
            $this->info('Nothing to optimize.');

            return self::SUCCESS;
        }

        if (! $dryRun) {
            $backupFile = $this->backup($posts);
            $this->info("Backup written to {$backupFile}");
        }

        $rows = [];
        $totalBefore = 0;
        $totalAfter = 0;

        foreach ($posts as $post) {
            $before = strlen((string) $post->content);
            $images = preg_match_all('#data:image/[a-z+]+;base64,#i', (string) $post->content);
            $changes = [];

            if (! $dryRun && $images > 0) {
                $changes['content'] = $this->imageService->extractInlineImages($post->content);
            }

            $featured = $withFeatured ? $this->featuredStatus($post->featured_image) : '-';
            if (! $dryRun && $featured === 'convert') {
                try {
                    $changes['featured_image'] = $this->imageService->store(
                        File::get($this->localPath($post->featured_image)),
                        'uploads/posts',
                        ImageService::FEATURED_VARIANTS
                    );
                    $featured = 'converted';
                } catch (Throwable $e) {
                    $featured = 'failed';
                    $this->warn("Post {$post->id}: featured image failed: {$e->getMessage()}");
                }
            }

            if ($changes !== []) {
                // Query builder keeps updated_at and model events untouched.
                DB::table('posts')->where('id', $post->id)->update($changes);
            }

            $after = strlen((string) ($changes['content'] ?? $post->content));
            $totalBefore += $before;
            $totalAfter += $after;

            $rows[] = [$post->id, mb_strimwidth((string) $post->title, 0, 40, '…'), $images, $this->kb($before), $dryRun ? '-' : $this->kb($after), $featured];
        }

        $this->table(['ID', 'Title', 'Inline images', 'Content before', 'Content after', 'Featured'], $rows);
        $this->info(sprintf(
            '%s %d post(s); content %s -> %s.',
            $dryRun ? 'Would optimize' : 'Optimized',
            count($rows),
            $this->kb($totalBefore),
            $dryRun ? '?' : $this->kb($totalAfter)
        ));

        return self::SUCCESS;
    }

    /**
     * Write the original rows to storage/backups so the run can be undone with --restore.
     */
    private function backup($posts): string
    {
        $directory = storage_path('backups');
        File::ensureDirectoryExists($directory);

        $file = $directory.'/posts-images-'.now()->format('Ymd_His').'.json';
        File::put($file, json_encode(
            $posts->map(fn ($post) => ['id' => $post->id, 'content' => $post->content, 'featured_image' => $post->featured_image])->values(),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        ));

        return $file;
    }

    private function restore(string $file): int
    {
        if (! File::exists($file)) {
            $this->error("Backup file not found: {$file}");

            return self::FAILURE;
        }

        $rows = json_decode(File::get($file), true);

        if (! is_array($rows)) {
            $this->error('Backup file is not valid JSON.');

            return self::FAILURE;
        }

        foreach ($rows as $row) {
            DB::table('posts')->where('id', $row['id'])->update([
                'content' => $row['content'],
                'featured_image' => $row['featured_image'],
            ]);
        }

        $this->info('Restored '.count($rows).' post(s) from '.$file);

        return self::SUCCESS;
    }

    private function featuredStatus(?string $url): string
    {
        if (empty($url)) {
            return '-';
        }

        if (str_ends_with($url, '.webp')) {
            return 'already webp';
        }

        return File::exists($this->localPath($url)) ? 'convert' : 'missing file';
    }

    private function localPath(string $url): string
    {
        return public_path(ltrim((string) parse_url($url, PHP_URL_PATH), '/'));
    }

    private function kb(int $bytes): string
    {
        return $bytes >= 1048576 ? round($bytes / 1048576, 1).' MB' : round($bytes / 1024).' KB';
    }
}

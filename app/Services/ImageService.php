<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Throwable;

/**
 * Stores uploaded images as resized, metadata-free WebP files under public/.
 */
class ImageService
{
    public const MAX_WIDTH = 1600;

    public const QUALITY = 80;

    /** Widths of the extra copies generated for featured images (used for srcset). */
    public const FEATURED_VARIANTS = [800, 400];

    private const INLINE_IMAGE_PATTERN = '#data:image/(?:png|jpe?g|gif|webp);base64,([A-Za-z0-9+/=]+)#i';

    private ImageManager $manager;

    public function __construct()
    {
        // Auto-orient phone photos from EXIF, drop EXIF/GPS data, and only keep the first GIF frame.
        $this->manager = ImageManager::gd(autoOrientation: true, decodeAnimation: false, strip: true);
    }

    /**
     * Optimize an image and store it, returning its public URL.
     *
     * @param  UploadedFile|string  $source  Uploaded file or raw image bytes
     * @param  int[]  $variants  Additional widths to store as "<name>-<width>.webp", largest first
     */
    public function store(UploadedFile|string $source, string $directory = 'uploads/posts', array $variants = []): string
    {
        $image = $this->manager->read($source instanceof UploadedFile ? $source->getRealPath() : $source);

        $relativeDir = trim($directory, '/').'/'.now()->format('Y/m');
        $absoluteDir = public_path($relativeDir);
        File::ensureDirectoryExists($absoluteDir);

        $name = (string) Str::uuid();

        $image->scaleDown(width: self::MAX_WIDTH)
            ->toWebp(self::QUALITY)
            ->save("{$absoluteDir}/{$name}.webp");

        // Each variant is scaled down from the previous (larger) one.
        rsort($variants);
        foreach ($variants as $width) {
            $image->scaleDown(width: $width)
                ->toWebp(self::QUALITY)
                ->save("{$absoluteDir}/{$name}-{$width}.webp");
        }

        return asset("{$relativeDir}/{$name}.webp");
    }

    /**
     * Replace base64 images embedded in HTML with stored image URLs.
     */
    public function extractInlineImages(?string $html, string $directory = 'uploads/posts'): ?string
    {
        if ($html === null || ! str_contains($html, 'data:image/')) {
            return $html;
        }

        return preg_replace_callback(self::INLINE_IMAGE_PATTERN, function (array $match) use ($directory) {
            $bytes = base64_decode($match[1], true);

            if ($bytes === false) {
                return $match[0];
            }

            try {
                return $this->store($bytes, $directory);
            } catch (Throwable $e) {
                Log::warning('Could not extract inline image: '.$e->getMessage());

                return $match[0];
            }
        }, $html);
    }

    /**
     * Build a srcset value for an image stored with variants, or null when it has none.
     */
    public function srcset(?string $url): ?string
    {
        if (empty($url) || ! str_ends_with($url, '.webp')) {
            return null;
        }

        $absolute = public_path(ltrim((string) parse_url($url, PHP_URL_PATH), '/'));
        $size = File::exists($absolute) ? @getimagesize($absolute) : false;

        if ($size === false) {
            return null;
        }

        $baseUrl = substr($url, 0, -strlen('.webp'));
        $baseFile = substr($absolute, 0, -strlen('.webp'));
        $sources = [];

        foreach (array_reverse(self::FEATURED_VARIANTS) as $width) {
            if ($width < $size[0] && File::exists("{$baseFile}-{$width}.webp")) {
                $sources[] = "{$baseUrl}-{$width}.webp {$width}w";
            }
        }

        if ($sources === []) {
            return null;
        }

        $sources[] = "{$url} {$size[0]}w";

        return implode(', ', $sources);
    }

    /**
     * Add native lazy loading to images in HTML content that do not set it already.
     */
    public function withLazyLoading(?string $html): ?string
    {
        if ($html === null || ! str_contains($html, '<img')) {
            return $html;
        }

        return preg_replace('/<img\b(?![^>]*\bloading=)/i', '<img loading="lazy" decoding="async"', $html);
    }

    /**
     * Give images in HTML content that have no alt attribute a text alternative. The post editor
     * (Quill) has no alt field, so article photos would otherwise be read out by file name.
     */
    public function withAltFallback(?string $html, string $alt): ?string
    {
        if ($html === null || ! str_contains($html, '<img')) {
            return $html;
        }

        return preg_replace_callback('/<img\b(?![^>]*\balt=)/i', fn () => '<img alt="' . e($alt) . '"', $html);
    }

    /**
     * Delete an image stored under public/ together with its generated variants.
     */
    public function delete(?string $url): void
    {
        if (empty($url)) {
            return;
        }

        $path = ltrim((string) parse_url($url, PHP_URL_PATH), '/');

        if ($path === '' || str_contains($path, '..')) {
            return;
        }

        $absolute = public_path($path);
        $files = [$absolute];

        if (str_ends_with($absolute, '.webp')) {
            $base = substr($absolute, 0, -strlen('.webp'));
            foreach (self::FEATURED_VARIANTS as $width) {
                $files[] = "{$base}-{$width}.webp";
            }
        }

        foreach ($files as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }
    }
}

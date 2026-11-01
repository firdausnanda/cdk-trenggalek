<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Term;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /** Cleared whenever a post is saved or deleted (see Post::booted()). */
    public const CACHE_KEY = 'sitemap.entries';

    public function sitemap(): Response
    {
        // Only slugs and dates are cached; URLs are built per request so they follow the host.
        [$posts, $kategori] = Cache::remember(self::CACHE_KEY, now()->addHour(), fn () => [
            Post::type('post')
                ->published()
                ->orderByDesc('published_at')
                ->get(['slug', 'featured_image', 'meta', 'published_at', 'updated_at'])
                ->reject(fn (Post $post) => $post->seo('noindex'))
                ->values()
                ->map(fn (Post $post) => [
                    'slug' => $post->slug,
                    'lastmod' => ($post->updated_at ?? $post->published_at)?->toAtomString(),
                    'gambar' => $post->featured_image,
                ])
                ->all(),
            Term::where('taxonomy', 'category')
                ->whereHas('posts', fn ($query) => $query->type('post')->published())
                ->orderBy('name')
                ->pluck('slug')
                ->all(),
        ]);

        return response()
            ->view('public.sitemap', compact('posts', 'kategori'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $isi = implode("\n", [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /login',
            'Disallow: /profile',
            'Disallow: /locale/',
            '',
            'Sitemap: ' . route('sitemap'),
            '',
        ]);

        return response($isi)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}

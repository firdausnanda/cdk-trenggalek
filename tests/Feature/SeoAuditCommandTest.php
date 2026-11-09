<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SeoAuditCommandTest extends TestCase
{
    private function page(string $url, array $override = []): string
    {
        $parts = [
            'title' => '<title>Penanaman mangrove di pesisir Trenggalek</title>',
            'description' => '<meta name="description" content="Kegiatan penanaman mangrove bersama kelompok tani hutan di pesisir selatan Trenggalek.">',
            'canonical' => '<link rel="canonical" href="' . $url . '">',
            'robots' => '<meta name="robots" content="index, follow">',
            'og' => '<meta property="og:title" content="x"><meta property="og:description" content="x"><meta property="og:image" content="https://example.test/og.jpg">',
            'jsonld' => '<script type="application/ld+json">{"@graph":[{"@type":"WebSite"}]}</script>',
            'body' => '<h1>Penanaman mangrove</h1><img src="a.jpg" alt="">',
            ...$override,
        ];

        return '<html><head>' . implode('', array_diff_key($parts, ['body' => 1])) . '</head><body>' . $parts['body'] . '</body></html>';
    }

    private function fakeSite(array $pages): void
    {
        $urls = implode('', array_map(fn ($url) => "<url><loc>{$url}</loc></url>", array_keys($pages)));

        Http::fake([
            'https://example.test/robots.txt' => Http::response("User-agent: *\nDisallow: /admin\n\nSitemap: https://example.test/sitemap.xml\n"),
            'https://example.test/sitemap.xml' => Http::response('<?xml version="1.0"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . $urls . '</urlset>'),
            ...array_map(fn ($html) => Http::response($html), $pages),
        ]);
    }

    #[Test]
    public function a_healthy_site_passes(): void
    {
        $this->fakeSite(['https://example.test/berita' => $this->page('https://example.test/berita')]);

        $this->artisan('seo:audit', ['url' => 'https://example.test'])
            ->expectsOutputToContain('Audited 1 page(s): 0 error(s), 0 warning(s).')
            ->assertSuccessful();
    }

    #[Test]
    public function broken_pages_are_reported_and_fail_the_command(): void
    {
        $this->fakeSite([
            'https://example.test/a' => $this->page('https://example.test/a', [
                'canonical' => '<link rel="canonical" href="https://example.test/lain">',
                'robots' => '<meta name="robots" content="noindex, follow">',
                'jsonld' => '<script type="application/ld+json">{"@graph":[{"@type":"NewsArticle","headline":"x"}]}</script>',
                'body' => '<img src="a.jpg">',
            ]),
            'https://example.test/b' => $this->page('https://example.test/b'),
        ]);

        $this->artisan('seo:audit', ['url' => 'https://example.test'])
            ->expectsOutputToContain('Canonical points elsewhere')
            ->expectsOutputToContain('marked noindex')
            ->expectsOutputToContain('NewsArticle is missing: image, datePublished, author')
            ->expectsOutputToContain('0 h1 headings')
            ->expectsOutputToContain('without an alt attribute')
            ->expectsOutputToContain('Same title on 2 pages')
            ->expectsOutputToContain('3 error(s)')
            ->assertFailed();
    }

    #[Test]
    public function a_missing_sitemap_fails(): void
    {
        Http::fake([
            'https://example.test/robots.txt' => Http::response("User-agent: *\nDisallow: /\n"),
            '*' => Http::response('', 404),
        ]);

        $this->artisan('seo:audit', ['url' => 'https://example.test'])
            ->expectsOutputToContain('blocks the whole site')
            ->expectsOutputToContain('Sitemap is missing (404)')
            ->assertFailed();
    }
}

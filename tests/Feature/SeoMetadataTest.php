<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SeoMetadataTest extends TestCase
{
    use RefreshDatabase;

    private function publishedPost(array $attributes = []): Post
    {
        return Post::factory()->create([
            'user_id' => User::factory()->create()->id,
            'post_type' => 'post',
            'status' => 'publish',
            'published_at' => now()->subDay(),
            ...$attributes,
        ]);
    }

    #[Test]
    public function public_pages_have_a_canonical_url_without_query_string_and_social_tags(): void
    {
        $this->get(route('tentang-kami', ['utm_source' => 'wa']))
            ->assertOk()
            ->assertSee('<link rel="canonical" href="' . route('tentang-kami') . '">', false)
            ->assertSee('<meta name="robots" content="index, follow">', false)
            ->assertSee('<meta property="og:image" content="' . asset('images/og-default.jpg') . '">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
    }

    #[Test]
    public function the_homepage_has_its_own_title(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>Cabang Dinas Kehutanan Wilayah Trenggalek, Jawa Timur</title>', false);
    }

    #[Test]
    public function titles_and_descriptions_fit_search_results(): void
    {
        $this->assertSame('Kontak · CDK Wilayah Trenggalek', judul_halaman('Kontak'));
        $judulPanjang = str_repeat('Penanaman bibit mangrove ', 3);
        $this->assertSame($judulPanjang, judul_halaman($judulPanjang));

        $this->assertSame('Bibit & pupuk.', deskripsi_meta('<p>Bibit &amp; pupuk.</p>'));
        $panjang = deskripsi_meta(str_repeat('<p>Kegiatan penanaman pohon di hutan rakyat.</p> ', 10));
        $this->assertLessThanOrEqual(160, mb_strlen($panjang));
        $this->assertStringEndsWith('…', $panjang);
        $this->assertMatchesRegularExpression('/(rakyat|Kegiatan|penanaman|pohon|di|hutan)…$/u', $panjang, 'Cut mid-word.');
    }

    #[Test]
    public function news_search_results_are_not_indexed(): void
    {
        $this->publishedPost(['title' => 'Penanaman pohon di Trenggalek']);

        $this->get(route('berita', ['search' => 'pohon']))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee('<link rel="canonical" href="' . route('berita') . '">', false);
    }

    #[Test]
    public function later_news_pages_get_their_own_title_and_canonical(): void
    {
        foreach (range(1, 10) as $i) {
            $this->publishedPost();
        }

        $this->get(route('berita', ['page' => 2]))
            ->assertOk()
            ->assertSee('<title>Berita · Halaman 2 · ', false)
            ->assertSee('<meta name="robots" content="index, follow">', false)
            ->assertSee('<link rel="canonical" href="' . route('berita', ['page' => 2]) . '">', false);

        $this->get(route('berita', ['page' => 1]))
            ->assertSee('<link rel="canonical" href="' . route('berita') . '">', false);

        $this->get(route('berita', ['page' => 9]))
            ->assertSee('<meta name="robots" content="noindex, follow">', false);
    }

    #[Test]
    public function a_news_article_shares_as_an_article_with_its_own_image(): void
    {
        $post = $this->publishedPost(['featured_image' => 'http://localhost/uploads/posts/foto.webp']);

        $this->get(route('berita.show', ['slug' => $post->slug, 'fbclid' => 'abc']))
            ->assertOk()
            ->assertSee('<link rel="canonical" href="' . route('berita.show', $post->slug) . '">', false)
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('<meta property="og:image" content="http://localhost/uploads/posts/foto.webp">', false)
            ->assertSee('<meta property="article:published_time" content="' . $post->published_at->toIso8601String() . '">', false);
    }

    #[Test]
    public function pages_carry_valid_structured_data(): void
    {
        $post = $this->publishedPost(['title' => 'Bibit </script> gratis & mudah']);

        $types = fn (string $url) => collect($this->jsonLd($this->get($url)->assertOk()->getContent()))
            ->flatMap(fn (array $graph) => $graph['@graph'])
            ->pluck('@type')
            ->all();

        $this->assertSame(['GovernmentOrganization', 'WebSite'], $types(route('home')));
        $this->assertSame(['GovernmentOrganization', 'WebSite', 'NewsArticle', 'BreadcrumbList'], $types(route('berita.show', $post->slug)));
        $this->assertContains('GovernmentOffice', $types(route('kontak')));

        $artikel = collect($this->jsonLd($this->get(route('berita.show', $post->slug))->getContent())[1]['@graph'])
            ->firstWhere('@type', 'NewsArticle');
        $this->assertSame('Bibit </script> gratis & mudah', $artikel['headline']);
    }

    /**
     * Decode every JSON-LD block on a page; fails the test if one is not valid JSON.
     */
    private function jsonLd(string $html): array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);

        return array_map(fn (string $json) => json_decode($json, true, flags: JSON_THROW_ON_ERROR), $matches[1]);
    }
}

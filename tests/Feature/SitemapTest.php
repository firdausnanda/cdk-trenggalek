<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    private function newsPost(array $attributes = []): Post
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
    public function the_sitemap_lists_pages_and_published_news_only(): void
    {
        $terbit = $this->newsPost(['featured_image' => 'http://localhost/uploads/posts/foto.webp']);
        $draf = $this->newsPost(['status' => 'draft']);
        $terjadwal = $this->newsPost(['published_at' => now()->addWeek()]);

        $response = $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'Sitemap is not valid XML.');

        $response->assertSee('<loc>' . route('home') . '</loc>', false)
            ->assertSee('<loc>' . route('kontak') . '</loc>', false)
            ->assertSee('<loc>' . route('berita.show', $terbit->slug) . '</loc>', false)
            ->assertSee('<image:loc>http://localhost/uploads/posts/foto.webp</image:loc>', false)
            ->assertDontSee(route('berita.show', $draf->slug), false)
            ->assertDontSee(route('berita.show', $terjadwal->slug), false);
    }

    #[Test]
    public function a_new_post_shows_up_in_the_cached_sitemap(): void
    {
        $this->get('/sitemap.xml')->assertOk();

        $baru = $this->newsPost();

        $this->get('/sitemap.xml')->assertSee(route('berita.show', $baru->slug), false);
    }

    #[Test]
    public function robots_txt_points_to_the_sitemap_and_keeps_admin_out(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap: ' . route('sitemap'));
    }
}

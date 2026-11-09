<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Category and tag URLs, per-post SEO overrides, and article image alt text.
 */
class NewsArchiveSeoTest extends TestCase
{
    use RefreshDatabase;

    private function newsPost(array $attributes = [], array $terms = []): Post
    {
        $post = Post::factory()->create([
            'user_id' => User::factory()->create()->id,
            'post_type' => 'post',
            'status' => 'publish',
            'published_at' => now()->subDay(),
            ...$attributes,
        ]);
        $post->terms()->attach(collect($terms)->pluck('id'));

        return $post;
    }

    private function term(string $taxonomy, string $name, string $slug): Term
    {
        return Term::create(['taxonomy' => $taxonomy, 'name' => $name, 'slug' => $slug]);
    }

    #[Test]
    public function old_category_query_links_redirect_permanently_to_the_category_url(): void
    {
        $this->get('/berita?kategori=rehabilitasi-hutan&search=bibit')
            ->assertStatus(301)
            ->assertRedirect(route('berita.kategori', ['slug' => 'rehabilitasi-hutan', 'search' => 'bibit']));
    }

    #[Test]
    public function a_category_page_lists_only_its_posts_and_is_indexable(): void
    {
        $kategori = $this->term('category', 'Rehabilitasi Hutan', 'rehabilitasi-hutan');
        $masuk = $this->newsPost(['title' => 'Penanaman mangrove'], [$kategori]);
        $lain = $this->newsPost(['title' => 'Rapat koordinasi']);

        $this->get(route('berita.kategori', 'rehabilitasi-hutan'))
            ->assertOk()
            ->assertSee('<title>Berita Rehabilitasi Hutan · ', false)
            ->assertSee('<meta name="robots" content="index, follow">', false)
            ->assertSee('<link rel="canonical" href="' . route('berita.kategori', 'rehabilitasi-hutan') . '">', false)
            ->assertSee($masuk->title)
            ->assertDontSee($lain->title);

        $this->get(route('berita.kategori', 'tidak-ada'))->assertNotFound();
        $this->get('/sitemap.xml')->assertSee('<loc>' . route('berita.kategori', 'rehabilitasi-hutan') . '</loc>', false);
    }

    #[Test]
    public function article_tags_link_to_a_tag_page_that_is_followed_but_not_indexed(): void
    {
        $tag = $this->term('tag', 'Mangrove', 'mangrove');
        $post = $this->newsPost(['title' => 'Penanaman mangrove'], [$tag]);

        $this->get(route('berita.show', $post->slug))
            ->assertSee('href="' . route('berita.tag', 'mangrove') . '"', false);

        $this->get(route('berita.tag', 'mangrove'))
            ->assertOk()
            ->assertSee('Topik #Mangrove')
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee($post->title);
    }

    #[Test]
    public function seo_overrides_replace_the_title_description_and_can_hide_a_post(): void
    {
        $post = $this->newsPost([
            'title' => 'Judul asli',
            'meta' => ['seo' => ['title' => 'Judul untuk Google', 'description' => 'Deskripsi khusus.', 'noindex' => true]],
        ]);

        $this->get(route('berita.show', $post->slug))
            ->assertSee('<title>Judul untuk Google · ', false)
            ->assertSee('<meta name="description" content="Deskripsi khusus.">', false)
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee('<h1', false)
            ->assertSee('Judul asli');

        $this->get('/sitemap.xml')->assertDontSee(route('berita.show', $post->slug), false);
    }

    #[Test]
    public function the_post_form_saves_seo_overrides_alongside_custom_fields(): void
    {
        Permission::create(['name' => 'post.edit']);
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'Editor'])->givePermissionTo('post.edit'));
        $post = $this->newsPost(['title' => 'Judul asli']);

        $simpan = fn (array $data) => $this->actingAs($user)->put(route('admin.posts.update', ['post', $post->id]), [
            'title' => 'Judul asli',
            'status' => 'publish',
            'content' => '<p>Isi</p>',
            'meta_keys' => ['lokasi'],
            'meta_values' => ['Trenggalek'],
            ...$data,
        ])->assertRedirect();

        $simpan(['seo_title' => ' Judul untuk Google ', 'seo_description' => 'Deskripsi khusus.', 'seo_noindex' => '1']);
        $post->refresh();
        $this->assertSame(['title' => 'Judul untuk Google', 'description' => 'Deskripsi khusus.', 'noindex' => true], $post->meta['seo']);
        $this->assertSame('Trenggalek', $post->getMeta('lokasi'));

        $simpan([]);
        $this->assertNull($post->refresh()->seo('title'));
    }

    #[Test]
    public function article_images_without_alt_text_get_one(): void
    {
        $post = $this->newsPost([
            'title' => 'Penanaman mangrove',
            'content' => '<p><img src="/uploads/a.webp"> <img src="/uploads/b.webp" alt="Bibit siap tanam"></p>',
        ]);

        $this->get(route('berita.show', $post->slug))
            ->assertSee('<img loading="lazy" decoding="async" alt="Dokumentasi kegiatan: Penanaman mangrove" src="/uploads/a.webp">', false)
            ->assertSee('alt="Bibit siap tanam"', false);
    }
}

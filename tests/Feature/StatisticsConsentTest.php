<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Support\StatisticsConsent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StatisticsConsentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_news_visit_is_recorded_only_after_statistics_consent(): void
    {
        $post = Post::factory()->create([
            'user_id' => User::factory()->create()->id,
            'post_type' => 'post',
            'status' => 'publish',
            'published_at' => now(),
        ]);
        $path = route('berita.show', $post->slug, false);

        $this->get($path)
            ->assertOk()
            ->assertSee('Pilihan Cookie')
            ->assertCookieMissing(config('eloquent-viewable.visitor_cookie_key'));
        $this->assertDatabaseCount('views', 0);

        $this->post(route('cookie-consent.store'), ['choice' => 'accept', 'return_to' => $path])
            ->assertStatus(303)
            ->assertRedirect($path)
            ->assertCookie(StatisticsConsent::COOKIE_NAME, StatisticsConsent::ACCEPT);

        $this->withCookie(StatisticsConsent::COOKIE_NAME, StatisticsConsent::ACCEPT)
            ->get($path)
            ->assertOk()
            ->assertDontSee('Pilihan Cookie')
            ->assertCookie(config('eloquent-viewable.visitor_cookie_key'));
        $this->assertDatabaseCount('views', 1);
    }

    #[Test]
    public function rejecting_statistics_removes_the_visitor_cookie_and_keeps_news_readable(): void
    {
        $post = Post::factory()->create([
            'user_id' => User::factory()->create()->id,
            'post_type' => 'post',
            'status' => 'publish',
            'published_at' => now(),
        ]);
        $path = route('berita.show', $post->slug, false);

        $this->withCookie(config('eloquent-viewable.visitor_cookie_key'), 'existing-visitor')
            ->post(route('cookie-consent.store'), ['choice' => 'reject', 'return_to' => $path])
            ->assertStatus(303)
            ->assertRedirect($path)
            ->assertCookie(StatisticsConsent::COOKIE_NAME, StatisticsConsent::REJECT)
            ->assertCookieExpired(config('eloquent-viewable.visitor_cookie_key'));

        $this->withCookie(StatisticsConsent::COOKIE_NAME, StatisticsConsent::REJECT)
            ->get($path)
            ->assertOk()
            ->assertDontSee('Pilihan Cookie');
        $this->assertDatabaseCount('views', 0);
    }
}

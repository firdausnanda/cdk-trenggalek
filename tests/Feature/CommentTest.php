<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Commenter;
use App\Models\Post;
use App\Models\User;
use App\Services\CommentModeration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.google.client_id' => 'test-client',
            'services.google.client_secret' => 'test-secret',
        ]);

        $this->post = Post::factory()->create([
            'user_id' => User::factory()->create()->id,
            'status' => 'publish',
            'published_at' => now()->subDay(),
        ]);
    }

    private function commenter(array $attributes = []): Commenter
    {
        static $n = 0;
        $n++;

        return Commenter::create($attributes + [
            'google_id' => 'g-'.$n,
            'name' => 'Pengunjung '.$n,
            'email' => "pengunjung{$n}@example.test",
        ]);
    }

    private function send(Commenter $as, string $text, array $extra = [])
    {
        return $this->actingAs($as, 'commenter')
            ->postJson(route('komentar.store', $this->post->id), ['comment' => $text] + $extra);
    }

    #[Test]
    public function guest_cannot_comment(): void
    {
        $this->postJson(route('komentar.store', $this->post->id), ['comment' => 'Halo'])
            ->assertUnauthorized();

        $this->assertDatabaseCount('comments', 0);
    }

    #[Test]
    public function guest_sees_google_sign_in_button(): void
    {
        $this->get(route('berita.show', $this->post->slug))
            ->assertOk()
            ->assertSee(__('public.comments.signin_button'))
            ->assertSee(__('public.comments.signin_short'))
            ->assertSee(route('komentar.google', ['kembali' => '/berita/'.$this->post->slug.'#komentar-tulis']), false)
            ->assertDontSee('name="comment"', false);
    }

    #[Test]
    public function sign_in_button_is_shown_disabled_until_google_is_configured(): void
    {
        config(['services.google.client_id' => null]);

        $this->get(route('berita.show', $this->post->slug))
            ->assertOk()
            ->assertSee(__('public.comments.signin_button'))
            ->assertSee(__('public.comments.unavailable'))
            ->assertDontSee(route('komentar.google'), false);
    }

    #[Test]
    public function signed_in_visitor_comment_goes_live_straight_away(): void
    {
        $visitor = $this->commenter();

        $this->send($visitor, "Kegiatan yang <b>bagus</b>!")
            ->assertCreated()
            ->assertJsonPath('status', Comment::STATUS_PUBLISHED)
            ->assertJsonStructure(['html', 'date', 'date_label', 'message']);

        $comment = Comment::sole();
        $this->assertSame('Kegiatan yang bagus!', $comment->content);
        $this->assertSame($visitor->id, $comment->commenter_id);

        // Another reader sees it on the page.
        $this->get(route('berita.show', $this->post->slug))->assertSee('Kegiatan yang bagus!');
    }

    #[Test]
    public function links_and_filtered_words_wait_for_review_and_only_the_author_sees_them(): void
    {
        config(['settings.'.CommentModeration::SETTING_BLOCKED_WORDS => "kata-terlarang\nfrasa jelek"]);
        $author = $this->commenter();

        $this->send($author, 'Cek https://promo.example')->assertJsonPath('status', Comment::STATUS_PENDING_REVIEW);
        $this->send($author, 'Ini KATA-TERLARANG sekali')->assertJsonPath('status', Comment::STATUS_PENDING_REVIEW);
        $this->send($author, 'Ini frasa jelek ya')->assertJsonPath('status', Comment::STATUS_PENDING_REVIEW);
        $this->send($author, 'Kata terlarang tanpa tanda hubung aman')->assertJsonPath('status', Comment::STATUS_PUBLISHED);

        $this->app['auth']->forgetGuards();
        $this->get(route('berita.show', $this->post->slug))->assertDontSee('KATA-TERLARANG');

        $this->actingAs($author, 'commenter')
            ->get(route('berita.show', $this->post->slug))
            ->assertSee('KATA-TERLARANG')
            ->assertSee(e(__('public.comments.status_pending')), false);
    }

    #[Test]
    public function visitor_is_rate_limited_and_cannot_send_duplicates(): void
    {
        $visitor = $this->commenter();
        RateLimiter::clear('komentar:'.$visitor->id);

        $this->send($visitor, 'Pesan sama')->assertCreated();
        $this->send($visitor, 'Pesan sama')->assertStatus(422);

        for ($i = 2; $i <= CommentModeration::RATE_LIMIT; $i++) {
            $this->send($visitor, "Pesan ke-{$i}")->assertCreated();
        }

        $this->send($visitor, 'Satu lagi')->assertStatus(429);
    }

    #[Test]
    public function blocked_visitor_cannot_comment_and_their_comments_disappear(): void
    {
        $visitor = $this->commenter();
        $this->send($visitor, 'Komentar lama saya')->assertCreated();

        $visitor->update(['blocked_at' => now()]);

        $this->send($visitor, 'Komentar baru')->assertForbidden();
        $this->app['auth']->forgetGuards();
        $this->get(route('berita.show', $this->post->slug))->assertDontSee('Komentar lama saya');
    }

    #[Test]
    public function reply_must_belong_to_the_same_post(): void
    {
        $visitor = $this->commenter();
        $this->send($visitor, 'Induk')->assertCreated();
        $parent = Comment::sole();

        $this->send($this->commenter(), 'Balasan', ['parent_id' => $parent->id])
            ->assertCreated()
            ->assertSee('Induk');

        $other = Post::factory()->create(['user_id' => $this->post->user_id, 'status' => 'publish', 'published_at' => now()->subDay()]);
        $foreign = Comment::create(['post_id' => $other->id, 'name' => 'x', 'content' => 'x', 'status' => 'published']);

        $this->send($visitor, 'Balasan salah', ['parent_id' => $foreign->id])->assertStatus(422);
    }

    #[Test]
    public function comment_is_hidden_once_reports_reach_the_threshold(): void
    {
        config(['settings.'.CommentModeration::SETTING_REPORT_THRESHOLD => 2]);
        $author = $this->commenter();
        $this->send($author, 'Komentar yang dilaporkan')->assertCreated();
        $comment = Comment::sole();

        // Own comment cannot be reported.
        $this->actingAs($author, 'commenter')
            ->postJson(route('komentar.report', $comment), ['reason' => 'spam'])
            ->assertStatus(422);

        $first = $this->commenter();
        $this->actingAs($first, 'commenter')
            ->postJson(route('komentar.report', $comment), ['reason' => 'spam'])
            ->assertOk()->assertJsonPath('hidden', false);

        // Reporting twice does not count twice.
        $this->actingAs($first, 'commenter')
            ->postJson(route('komentar.report', $comment), ['reason' => 'abusive'])
            ->assertOk()->assertJsonPath('message', __('public.comments.already_reported'));
        $this->assertSame(1, $comment->fresh()->reports_count);

        $this->actingAs($this->commenter(), 'commenter')
            ->postJson(route('komentar.report', $comment), ['reason' => 'abusive'])
            ->assertOk()->assertJsonPath('hidden', true);

        $this->assertSame(Comment::STATUS_HIDDEN, $comment->fresh()->status);
    }

    #[Test]
    public function visitor_can_delete_only_their_own_comment(): void
    {
        $author = $this->commenter();
        $this->send($author, 'Milik saya')->assertCreated();
        $comment = Comment::sole();

        $this->actingAs($this->commenter(), 'commenter')
            ->deleteJson(route('komentar.destroy', $comment))
            ->assertForbidden();

        $this->actingAs($author, 'commenter')
            ->deleteJson(route('komentar.destroy', $comment))
            ->assertOk();

        $this->assertDatabaseCount('comments', 0);
    }

    #[Test]
    public function google_callback_signs_the_visitor_in_and_returns_to_the_article(): void
    {
        Http::fake(['*' => Http::response('fake-image', 200, ['Content-Type' => 'image/jpeg'])]);

        $google = (new GoogleUser())->map([
            'id' => '1234567890',
            'name' => 'Sari Rimbawati',
            'email' => 'sari@example.test',
            'avatar' => 'https://lh3.googleusercontent.com/a/photo',
        ]);
        Socialite::shouldReceive('driver->user')->andReturn($google);

        $path = '/berita/'.$this->post->slug;

        $this->withSession(['commenter.return_to' => $path.'#komentar-tulis'])
            ->get(route('komentar.google.callback', ['code' => 'abc', 'state' => 'x']))
            ->assertRedirect(url($path.'#komentar-tulis'));

        $commenter = Commenter::sole();
        $this->assertSame('Sari Rimbawati', $commenter->name);
        $this->assertStringStartsWith('/uploads/commenters/'.$commenter->id.'.jpg', $commenter->avatar);
        $this->assertAuthenticatedAs($commenter, 'commenter');
        $this->assertGuest('web');

        @unlink(public_path('uploads/commenters/'.$commenter->id.'.jpg'));
    }

    #[Test]
    public function sign_in_redirect_rejects_offsite_return_urls(): void
    {
        Socialite::shouldReceive('driver->redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        $this->get(route('komentar.google', ['kembali' => '//evil.example/x']))
            ->assertRedirect('https://accounts.google.com/o/oauth2/auth')
            ->assertSessionHas('commenter.return_to', route('berita'));
    }

    #[Test]
    public function commenter_session_never_opens_the_admin_panel(): void
    {
        $this->actingAs($this->commenter(), 'commenter')
            ->get(route('admin.dashboard'))
            ->assertRedirect();
    }

    #[Test]
    public function admin_can_hide_restore_reply_and_block(): void
    {
        foreach (['comment.view', 'comment.edit', 'comment.create', 'comment.delete'] as $name) {
            Permission::create(['name' => $name]);
        }
        $admin = User::factory()->create();
        $admin->givePermissionTo(['comment.view', 'comment.edit', 'comment.create', 'comment.delete']);

        $visitor = $this->commenter();
        $this->send($visitor, 'Perlu dicek')->assertCreated();
        $comment = Comment::sole();
        $comment->update(['status' => Comment::STATUS_HIDDEN, 'reports_count' => 3]);

        $this->actingAs($admin, 'web')->get(route('admin.comment.index', ['status' => 'needs_review']))
            ->assertOk()->assertSee('Perlu dicek');

        $this->actingAs($admin, 'web')->post(route('admin.comment.publish', $comment))->assertRedirect();
        $this->assertSame(Comment::STATUS_PUBLISHED, $comment->fresh()->status);
        $this->assertSame(0, $comment->fresh()->reports_count);

        $this->actingAs($admin, 'web')->post(route('admin.comment.reply', $comment), ['content' => 'Terima kasih masukannya'])->assertRedirect();
        $reply = Comment::whereNotNull('user_id')->sole();
        $this->assertSame($comment->id, $reply->parent_id);
        $this->get(route('berita.show', $this->post->slug))
            ->assertSee('Terima kasih masukannya')
            ->assertSee(__('public.comments.admin_badge'));

        $this->actingAs($admin, 'web')->post(route('admin.commenters.block', $visitor))->assertRedirect();
        $this->assertTrue($visitor->fresh()->isBlocked());

        $this->actingAs($admin, 'web')->put(route('admin.comment.settings'), [
            'blocked_words' => "satu, dua\nsatu",
            'report_threshold' => 5,
        ])->assertRedirect();
        $this->assertDatabaseHas('settings', ['option_name' => CommentModeration::SETTING_BLOCKED_WORDS, 'option_value' => "satu\ndua"]);
    }
}

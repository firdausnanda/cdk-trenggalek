<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('kontak:127.0.0.1');
    }

    /** A payload as a person would send it: rendered a while ago, honeypot empty. */
    private function payload(array $overrides = [], int $renderedSecondsAgo = 10): array
    {
        return array_merge([
            'name' => "Siti Rahma, S.Hut.",
            'email' => 'warga@gmail.com',
            'message' => 'Saya ingin bertanya tentang izin pemanfaatan hutan.',
            'website' => '',
            '_ts' => encrypt(now()->subSeconds($renderedSecondsAgo)->timestamp),
        ], $overrides);
    }

    #[Test]
    public function a_genuine_message_is_saved(): void
    {
        $this->post(route('kontak_store'), $this->payload())
            ->assertRedirect(route('kontak') . '#form-kontak')
            ->assertSessionHas('success');

        $this->assertDatabaseCount(ContactMessage::class, 1);
    }

    #[Test]
    public function the_honeypot_silently_drops_bots(): void
    {
        $this->post(route('kontak_store'), $this->payload(['website' => 'http://spam.example']))
            ->assertSessionHas('success');

        $this->assertDatabaseCount(ContactMessage::class, 0);
    }

    #[Test]
    public function forms_sent_too_fast_or_without_a_valid_token_are_rejected(): void
    {
        $this->post(route('kontak_store'), $this->payload([], renderedSecondsAgo: 1))->assertSessionHasErrors('form');
        $this->post(route('kontak_store'), $this->payload(['_ts' => 'tampered']))->assertSessionHasErrors('form');
        $this->post(route('kontak_store'), $this->payload([], renderedSecondsAgo: 3 * 3600))->assertSessionHasErrors('form');

        $this->assertDatabaseCount(ContactMessage::class, 0);
    }

    #[Test]
    public function messages_with_too_many_links_or_odd_names_are_rejected(): void
    {
        $links = 'Cek https://a.example https://b.example https://c.example';
        $this->post(route('kontak_store'), $this->payload(['message' => $links]))->assertSessionHasErrors('message');
        $this->post(route('kontak_store'), $this->payload(['name' => '<script>x</script>']))->assertSessionHasErrors('name');
        $this->post(route('kontak_store'), $this->payload(['message' => 'halo']))->assertSessionHasErrors('message');

        $this->assertDatabaseCount(ContactMessage::class, 0);
    }

    #[Test]
    public function each_ip_may_only_send_a_few_messages_per_window(): void
    {
        foreach (range(1, 3) as $_) {
            $this->post(route('kontak_store'), $this->payload())->assertSessionHas('success');
        }

        $this->post(route('kontak_store'), $this->payload())->assertSessionHasErrors('form');
        $this->assertDatabaseCount(ContactMessage::class, 3);
    }
}

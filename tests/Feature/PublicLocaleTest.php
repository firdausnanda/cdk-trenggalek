<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Dual language (Bahasa Indonesia / English) on the public site: the language is kept in the session
 * by LocaleController and the interface text comes from lang/{id,en}/public.php.
 */
class PublicLocaleTest extends TestCase
{
    use RefreshDatabase;

    public static function publicRoutes(): array
    {
        return [
            'home' => ['home'],
            'tentang-kami' => ['tentang-kami'],
            'berita' => ['berita'],
            'kontak' => ['kontak'],
            'aksesibilitas' => ['aksesibilitas'],
            'kebijakan-privasi' => ['kebijakan-privasi'],
        ];
    }

    #[Test]
    public function public_pages_default_to_indonesian(): void
    {
        $this->get(route('tentang-kami'))
            ->assertOk()
            ->assertSee('<html lang="id"', false)
            ->assertSee('<meta property="og:locale" content="id_ID">', false)
            ->assertSee('Tugas dan fungsi')
            ->assertDontSee('Duties and functions');
    }

    #[Test]
    #[DataProvider('publicRoutes')]
    public function public_page_renders_in_english(string $routeName): void
    {
        $this->withSession(['locale' => 'en'])
            ->get(route($routeName))
            ->assertOk()
            ->assertSee('<html lang="en"', false)
            ->assertSee('<meta property="og:locale" content="en_US">', false)
            ->assertSee('Skip to content')
            ->assertDontSee('Langsung ke konten');
    }

    #[Test]
    public function visitor_can_switch_language_and_return_to_the_page(): void
    {
        $this->from(route('kontak'))
            ->get(route('locale.switch', 'en'))
            ->assertRedirect(route('kontak'))
            ->assertSessionHas('locale', 'en')
            // The public forms show session('success'), so switching must not flash a message there.
            ->assertSessionMissing('success');

        $this->get(route('kontak'))->assertSee('Send message');
    }

    #[Test]
    public function unknown_language_is_rejected(): void
    {
        $this->get('/locale/xx')->assertNotFound();
        $this->assertNull(session('locale'));
    }

    #[Test]
    public function invalid_session_locale_falls_back_to_the_default(): void
    {
        $this->withSession(['locale' => '../../etc'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('<html lang="id"', false);
    }

    #[Test]
    public function contact_form_validation_messages_follow_the_language(): void
    {
        $this->withSession(['locale' => 'en'])
            ->from(route('kontak'))
            ->post(route('kontak_store'), [])
            ->assertSessionHasErrors(['name' => 'Please enter your name.']);
    }

    #[Test]
    public function numbers_use_the_separators_of_the_language(): void
    {
        app()->setLocale('id');
        $this->assertSame('1.234,5', angka(1234.5, 1));
        $this->assertSame('Rp167,7 M', rupiah_singkat(167704808505));

        app()->setLocale('en');
        $this->assertSame('1,234.5', angka(1234.5, 1));
        $this->assertSame('Rp167.7 B', rupiah_singkat(167704808505));
        $this->assertSame('Rp2.5 M', rupiah_singkat(2500000));
    }

    #[Test]
    public function both_languages_define_the_same_public_keys(): void
    {
        $flatten = function (array $lines): array {
            $keys = [];
            foreach ($lines as $group => $entries) {
                foreach (array_keys($entries) as $key) {
                    $keys[] = "{$group}.{$key}";
                }
            }
            sort($keys);

            return $keys;
        };

        $this->assertSame(
            $flatten(require lang_path('id/public.php')),
            $flatten(require lang_path('en/public.php')),
        );
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use App\Services\EnvWriter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Jangan sampai test menulis ke file .env yang asli.
        $this->mock(EnvWriter::class)->shouldReceive('batchWriteKeysToEnvFile');

        Permission::create(['name' => 'settings.edit']);
        $this->admin = User::factory()->create();
        $this->admin->givePermissionTo('settings.edit');
    }

    protected function tearDown(): void
    {
        foreach (File::glob(public_path('uploads/settings/site_*_test_*')) as $file) {
            File::delete($file);
        }

        parent::tearDown();
    }

    #[Test]
    public function settings_page_only_shows_general_and_integration_tabs(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('name="app_name"', false)
            ->assertSee('name="default_pagination"', false)
            ->assertSee('name="site_icon"', false)
            ->assertSee('name="site_favicon"', false)
            ->assertSee('name="google_analytics_script"', false)
            ->assertDontSee('name="site_logo_lite"', false)
            ->assertDontSee('name="theme_primary_color"', false)
            ->assertDontSee('name="navbar_bg_lite"', false)
            ->assertDontSee('data-tab="appearance"', false)
            ->assertDontSee('data-tab="content"', false);
    }

    #[Test]
    public function store_only_saves_allowed_keys(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.store'), [
                'app_name' => 'CDK Trenggalek',
                'default_pagination' => 15,
                'google_analytics_script' => '<script>ga()</script>',
                'evil_key' => 'injected',
                'theme_primary_color_text' => '#000000',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('CDK Trenggalek', Setting::where('option_name', 'app_name')->value('option_value'));
        $this->assertSame('15', Setting::where('option_name', 'default_pagination')->value('option_value'));
        $this->assertDatabaseMissing('settings', ['option_name' => 'evil_key']);
        $this->assertDatabaseMissing('settings', ['option_name' => 'theme_primary_color_text']);
    }

    #[Test]
    public function store_validates_pagination(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.store'), [
                'app_name' => 'CDK',
                'default_pagination' => 0,
            ])
            ->assertSessionHasErrors('default_pagination');
    }

    #[Test]
    public function replacing_icon_deletes_previous_upload_but_never_bundled_assets(): void
    {
        File::ensureDirectoryExists(public_path('uploads/settings'));
        $old = public_path('uploads/settings/site_icon_test_old.png');
        File::put($old, 'old');
        config(['settings.site_icon' => asset('uploads/settings/site_icon_test_old.png')]);
        config(['settings.site_favicon' => '/images/logo/icon.webp']);

        $this->actingAs($this->admin)
            ->post(route('admin.settings.store'), [
                'app_name' => 'CDK',
                'default_pagination' => 10,
                'site_icon' => UploadedFile::fake()->image('site_icon_test_new.png'),
                'site_favicon' => UploadedFile::fake()->image('site_favicon_test_new.png'),
            ])
            ->assertSessionHasNoErrors();

        $this->assertFileDoesNotExist($old);
        $this->assertFileExists(public_path('images/logo/icon.webp'));

        // Rapikan file baru hasil upload test.
        foreach (['site_icon', 'site_favicon'] as $key) {
            $path = ltrim((string) parse_url((string) Setting::where('option_name', $key)->value('option_value'), PHP_URL_PATH), '/');
            File::delete(public_path($path));
        }
    }

    #[Test]
    public function analytics_script_is_rendered_on_public_site_not_admin(): void
    {
        config(['settings.google_analytics_script' => '<script id="ga-marker"></script>']);

        $this->get('/')->assertSee('ga-marker', false);

        $this->actingAs($this->admin)
            ->get(route('admin.settings.index'))
            ->assertDontSee('<script id="ga-marker"></script>', false);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Basic "does it still boot and render" checks, used as a safety net during framework upgrades.
 */
class SmokeTest extends TestCase
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
    #[DataProvider('publicRoutes')]
    public function public_page_renders(string $routeName): void
    {
        $this->get(route($routeName))->assertOk();
    }

    #[Test]
    public function guest_is_redirected_from_admin(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect();
        $this->assertGuest();
    }

    #[Test]
    public function user_with_permission_can_view_dashboard(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('UserChartService uses MySQL-only DATE_FORMAT().');
        }

        Permission::create(['name' => 'dashboard.view']);
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'Admin'])->givePermissionTo('dashboard.view'));

        $this->actingAs($user)->get(route('admin.dashboard'))->assertOk();
    }
}

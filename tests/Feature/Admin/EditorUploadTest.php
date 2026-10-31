<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EditorUploadTest extends TestCase
{
    use RefreshDatabase;

    private string $publicPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publicPath = sys_get_temp_dir().'/editor-upload-test-'.uniqid();
        File::ensureDirectoryExists($this->publicPath);
        $this->app->usePublicPath($this->publicPath);

        Permission::create(['name' => 'post.create']);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->publicPath);

        parent::tearDown();
    }

    #[Test]
    public function editor_can_upload_an_image_and_gets_a_webp_url(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('post.create');

        $response = $this->actingAs($user)->postJson(route('admin.editor.upload'), [
            'file' => UploadedFile::fake()->image('photo.jpg', 2400, 1600),
        ]);

        $response->assertOk();
        $url = $response->json('location');
        $this->assertStringEndsWith('.webp', $url);

        $size = getimagesize(public_path(ltrim(parse_url($url, PHP_URL_PATH), '/')));
        $this->assertSame([1600, 'image/webp'], [$size[0], $size['mime']]);
    }

    #[Test]
    public function user_without_post_permission_cannot_upload(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson(route('admin.editor.upload'), ['file' => UploadedFile::fake()->image('photo.jpg')])
            ->assertForbidden();
    }

    #[Test]
    public function non_image_files_are_rejected(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('post.create');

        $this->actingAs($user)
            ->postJson(route('admin.editor.upload'), ['file' => UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf')])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('file');
    }

    #[Test]
    public function guests_cannot_upload(): void
    {
        $this->postJson(route('admin.editor.upload'), ['file' => UploadedFile::fake()->image('photo.jpg')])
            ->assertUnauthorized();
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OptimizePostImagesCommandTest extends TestCase
{
    use RefreshDatabase;

    private string $publicPath;

    private string $backupPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publicPath = sys_get_temp_dir().'/optimize-images-public-'.uniqid();
        $this->backupPath = sys_get_temp_dir().'/optimize-images-storage-'.uniqid();
        File::ensureDirectoryExists($this->publicPath);
        File::ensureDirectoryExists($this->backupPath);
        $this->app->usePublicPath($this->publicPath);
        $this->app->useStoragePath($this->backupPath);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->publicPath);
        File::deleteDirectory($this->backupPath);

        parent::tearDown();
    }

    private function createPost(string $content, ?string $featuredImage = null): int
    {
        return DB::table('posts')->insertGetId([
            'title' => 'Post',
            'slug' => 'post-'.uniqid(),
            'content' => $content,
            'featured_image' => $featuredImage,
            'post_type' => 'post',
            'status' => 'publish',
            'user_id' => User::factory()->create()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function inlineImageHtml(): string
    {
        $jpeg = base64_encode(UploadedFile::fake()->image('inline.jpg', 2000, 1000)->get());

        return '<p>Text</p><p><img src="data:image/jpeg;base64,'.$jpeg.'"></p>';
    }

    #[Test]
    public function dry_run_does_not_change_anything(): void
    {
        $html = $this->inlineImageHtml();
        $id = $this->createPost($html);

        $this->artisan('posts:optimize-images', ['--dry-run' => true])->assertSuccessful();

        $this->assertSame($html, DB::table('posts')->where('id', $id)->value('content'));
        $this->assertDirectoryDoesNotExist(storage_path('backups'));
    }

    #[Test]
    public function it_moves_inline_images_to_files_and_can_be_restored(): void
    {
        $html = $this->inlineImageHtml();
        $id = $this->createPost($html);
        $updatedAt = DB::table('posts')->where('id', $id)->value('updated_at');

        $this->artisan('posts:optimize-images')->assertSuccessful();

        $content = DB::table('posts')->where('id', $id)->value('content');
        $this->assertStringNotContainsString('data:image/', $content);
        $this->assertMatchesRegularExpression('#<img src="[^"]+/uploads/posts/\d{4}/\d{2}/[0-9a-f-]+\.webp">#', $content);
        $this->assertSame($updatedAt, DB::table('posts')->where('id', $id)->value('updated_at'));

        // Running again finds nothing left to do.
        $this->artisan('posts:optimize-images')->expectsOutput('Nothing to optimize.')->assertSuccessful();

        $backup = File::files(storage_path('backups'))[0]->getPathname();
        $this->artisan('posts:optimize-images', ['--restore' => $backup])->assertSuccessful();

        $this->assertSame($html, DB::table('posts')->where('id', $id)->value('content'));
    }

    #[Test]
    public function it_converts_local_featured_images_when_requested(): void
    {
        File::ensureDirectoryExists(public_path('uploads/posts'));
        File::put(public_path('uploads/posts/cover.jpg'), UploadedFile::fake()->image('cover.jpg', 2400, 1600)->get());
        $id = $this->createPost('<p>No inline images</p>', 'http://localhost/uploads/posts/cover.jpg');
        $missingId = $this->createPost('<p>Missing</p>', 'http://localhost/uploads/posts/missing.jpg');

        $this->artisan('posts:optimize-images', ['--featured' => true])->assertSuccessful();

        $featured = DB::table('posts')->where('id', $id)->value('featured_image');
        $this->assertStringEndsWith('.webp', $featured);
        $path = public_path(ltrim(parse_url($featured, PHP_URL_PATH), '/'));
        $this->assertSame(1600, getimagesize($path)[0]);
        $this->assertFileExists(str_replace('.webp', '-400.webp', $path));

        // The original stays in place for rollback; missing files are left untouched.
        $this->assertFileExists(public_path('uploads/posts/cover.jpg'));
        $this->assertSame('http://localhost/uploads/posts/missing.jpg', DB::table('posts')->where('id', $missingId)->value('featured_image'));
    }
}

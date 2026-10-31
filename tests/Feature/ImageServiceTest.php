<?php

namespace Tests\Feature;

use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImageServiceTest extends TestCase
{
    private string $publicPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publicPath = sys_get_temp_dir().'/image-service-test-'.uniqid();
        File::ensureDirectoryExists($this->publicPath);
        $this->app->usePublicPath($this->publicPath);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->publicPath);

        parent::tearDown();
    }

    private function pathFromUrl(string $url): string
    {
        return public_path(ltrim(parse_url($url, PHP_URL_PATH), '/'));
    }

    #[Test]
    public function it_stores_large_images_as_downscaled_webp_with_variants(): void
    {
        $url = app(ImageService::class)->store(
            UploadedFile::fake()->image('photo.jpg', 3000, 2000),
            'uploads/posts',
            ImageService::FEATURED_VARIANTS
        );

        $this->assertStringEndsWith('.webp', $url);
        $path = $this->pathFromUrl($url);

        $this->assertSame([1600, 1067, 'image/webp'], array_values(array_intersect_key(getimagesize($path), array_flip([0, 1, 'mime']))));
        $this->assertSame(800, getimagesize(str_replace('.webp', '-800.webp', $path))[0]);
        $this->assertSame(400, getimagesize(str_replace('.webp', '-400.webp', $path))[0]);
    }

    #[Test]
    public function it_does_not_upscale_small_images(): void
    {
        $url = app(ImageService::class)->store(UploadedFile::fake()->image('small.png', 300, 200));

        $this->assertSame(300, getimagesize($this->pathFromUrl($url))[0]);
    }

    #[Test]
    public function it_replaces_inline_base64_images_with_urls(): void
    {
        $png = base64_encode(UploadedFile::fake()->image('inline.png', 50, 50)->get());
        $html = '<p>Hello</p><p><img src="data:image/png;base64,'.$png.'"></p>';

        $result = app(ImageService::class)->extractInlineImages($html);

        $this->assertStringNotContainsString('data:image/', $result);
        $this->assertMatchesRegularExpression('#<img src="http[^"]+/uploads/posts/\d{4}/\d{2}/[0-9a-f-]+\.webp">#', $result);
        $this->assertStringStartsWith('<p>Hello</p>', $result);
    }

    #[Test]
    public function it_leaves_undecodable_inline_images_untouched(): void
    {
        $html = '<img src="data:image/png;base64,bm90LWFuLWltYWdl">';

        $this->assertSame($html, app(ImageService::class)->extractInlineImages($html));
        $this->assertSame('<p>No images</p>', app(ImageService::class)->extractInlineImages('<p>No images</p>'));
        $this->assertNull(app(ImageService::class)->extractInlineImages(null));
    }

    #[Test]
    public function it_deletes_an_image_with_its_variants(): void
    {
        $service = app(ImageService::class);
        $url = $service->store(UploadedFile::fake()->image('photo.jpg', 1000, 800), 'uploads/posts', ImageService::FEATURED_VARIANTS);
        $path = $this->pathFromUrl($url);

        $service->delete($url);

        $this->assertFileDoesNotExist($path);
        $this->assertFileDoesNotExist(str_replace('.webp', '-800.webp', $path));
        $this->assertFileDoesNotExist(str_replace('.webp', '-400.webp', $path));
    }
}

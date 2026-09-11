<?php

namespace Tests\Feature;

use App\Services\ImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_jpeg_is_converted_to_webp(): void
    {
        Storage::fake('public');

        // Buat file JPEG palsu menggunakan GD
        $tmpPath = tempnam(sys_get_temp_dir(), 'test_img_') . '.jpg';
        $img = imagecreatetruecolor(200, 150);
        imagecolorallocate($img, 255, 0, 0); // merah
        imagejpeg($img, $tmpPath, 90);
        imagedestroy($img);

        $uploaded = new UploadedFile($tmpPath, 'test.jpg', 'image/jpeg', null, true);

        $svc  = app(ImageService::class);
        $path = $svc->storeAsWebp($uploaded, directory: 'products', quality: 80);

        // Path harus berakhiran .webp
        $this->assertStringEndsWith('.webp', $path);
        $this->assertStringStartsWith('products/', $path);

        // File harus tersimpan di disk public
        Storage::disk('public')->assertExists($path);

        // Isi file harus berupa WebP (magic bytes: RIFF....WEBP)
        $content = Storage::disk('public')->get($path);
        $this->assertStringStartsWith('RIFF', $content);
        $this->assertStringContainsString('WEBP', substr($content, 0, 12));

        @unlink($tmpPath);
    }

    public function test_png_with_transparency_is_converted_to_webp(): void
    {
        Storage::fake('public');

        $tmpPath = tempnam(sys_get_temp_dir(), 'test_img_') . '.png';
        $img = imagecreatetruecolor(100, 100);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $transparent);
        imagepng($img, $tmpPath);
        imagedestroy($img);

        $uploaded = new UploadedFile($tmpPath, 'test.png', 'image/png', null, true);

        $path = app(ImageService::class)->storeAsWebp($uploaded, directory: 'products');

        Storage::disk('public')->assertExists($path);
        $this->assertStringEndsWith('.webp', $path);

        @unlink($tmpPath);
    }

    public function test_image_is_resized_to_max_width(): void
    {
        Storage::fake('public');

        // Buat gambar besar 2000x1000
        $tmpPath = tempnam(sys_get_temp_dir(), 'test_img_') . '.jpg';
        $img = imagecreatetruecolor(2000, 1000);
        imagejpeg($img, $tmpPath, 85);
        imagedestroy($img);

        $uploaded = new UploadedFile($tmpPath, 'wide.jpg', 'image/jpeg', null, true);

        $path = app(ImageService::class)->storeAsWebp($uploaded, directory: 'products', maxWidth: 1200);

        Storage::disk('public')->assertExists($path);

        // Verifikasi lebar tidak melebihi 1200px
        $fullPath = Storage::disk('public')->path($path);
        [$w, $h]  = getimagesize($fullPath);
        $this->assertLessThanOrEqual(1200, $w);
        $this->assertEquals(600, $h, 'Tinggi harus proporsional (1000 * 1200/2000 = 600)');

        @unlink($tmpPath);
    }

    public function test_webp_file_size_is_smaller_than_jpeg(): void
    {
        Storage::fake('public');

        // Buat JPEG besar 1500x1000
        $tmpPath = tempnam(sys_get_temp_dir(), 'test_img_') . '.jpg';
        $img = imagecreatetruecolor(1500, 1000);
        // Isi dengan konten bervariasi agar tidak ter-compress sempurna
        for ($x = 0; $x < 1500; $x += 10) {
            for ($y = 0; $y < 1000; $y += 10) {
                $color = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
                imagefilledellipse($img, $x, $y, 5, 5, $color);
            }
        }
        imagejpeg($img, $tmpPath, 95); // JPEG kualitas tinggi
        imagedestroy($img);

        $jpegSize = filesize($tmpPath);

        $uploaded = new UploadedFile($tmpPath, 'large.jpg', 'image/jpeg', null, true);
        $path = app(ImageService::class)->storeAsWebp($uploaded, directory: 'products', quality: 82);

        $webpSize = Storage::disk('public')->size($path);

        // WebP dengan quality=82 seharusnya lebih kecil dari JPEG quality=95
        $this->assertLessThan($jpegSize, $webpSize, "WebP ({$webpSize}B) harus lebih kecil dari JPEG ({$jpegSize}B)");

        @unlink($tmpPath);
    }
}


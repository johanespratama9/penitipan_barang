<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SplFileInfo;

/**
 * ImageService – konversi semua upload gambar ke format WebP menggunakan GD native PHP.
 *
 * Mendukung Livewire TemporaryUploadedFile (Filament) maupun UploadedFile biasa.
 * Output: file WebP disimpan ke disk 'public', path dikembalikan untuk disimpan di DB.
 */
class ImageService
{
    /**
     * Simpan file gambar sebagai WebP ke storage disk 'public'.
     *
     * @param  UploadedFile|SplFileInfo  $file      File gambar yang di-upload
     * @param  string                    $directory  Folder tujuan di disk public (e.g. 'products')
     * @param  int                       $quality    Kualitas WebP 0–100 (default 82)
     * @param  int|null                  $maxWidth   Lebar maksimum px (null = tidak di-resize)
     * @return string  Path relatif di disk public (e.g. 'products/abc123.webp')
     */
    public function storeAsWebp(
        UploadedFile|SplFileInfo $file,
        string $directory = 'products',
        int $quality = 82,
        ?int $maxWidth = 1200
    ): string {
        $filename    = Str::uuid()->toString() . '.webp';
        $storagePath = $directory . '/' . $filename;

        // Resolve jalur fisik file
        $realPath = ($file instanceof UploadedFile)
            ? $file->getRealPath()
            : $file->getPathname();

        // Deteksi MIME type dari isi file (bukan ekstensi)
        $mimeType = mime_content_type($realPath) ?: 'image/jpeg';

        $source = $this->createGdImage($realPath, $mimeType);

        // Flatten transparansi PNG/GIF ke latar putih
        if (str_contains($mimeType, 'png') || str_contains($mimeType, 'gif')) {
            $bg = imagecreatetruecolor(imagesx($source), imagesy($source));
            imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
            imagecopy($bg, $source, 0, 0, 0, 0, imagesx($source), imagesy($source));
            imagedestroy($source);
            $source = $bg;
        }

        // Resize proporsional jika melebihi maxWidth
        if ($maxWidth && imagesx($source) > $maxWidth) {
            $origW   = imagesx($source);
            $origH   = imagesy($source);
            $newH    = (int) round($origH * $maxWidth / $origW);
            $resized = imagecreatetruecolor($maxWidth, $newH);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $maxWidth, $newH, $origW, $origH);
            imagedestroy($source);
            $source = $resized;
        }

        // Encode ke WebP dan simpan ke storage
        ob_start();
        imagewebp($source, null, $quality);
        $webpData = ob_get_clean();
        imagedestroy($source);

        Storage::disk('public')->put($storagePath, $webpData);

        return $storagePath;
    }

    /**
     * Buat GD image resource dari file, deteksi dari MIME type.
     *
     * @return \GdImage
     */
    private function createGdImage(string $path, string $mimeType): \GdImage
    {
        return match (true) {
            str_contains($mimeType, 'jpeg') => imagecreatefromjpeg($path),
            str_contains($mimeType, 'png')  => imagecreatefrompng($path),
            str_contains($mimeType, 'gif')  => imagecreatefromgif($path),
            str_contains($mimeType, 'webp') => imagecreatefromwebp($path),
            str_contains($mimeType, 'bmp')  => imagecreatefrombmp($path),
            // Fallback: coba deteksi dari header binary
            default => @imagecreatefromjpeg($path)
                ?: @imagecreatefrompng($path)
                ?: @imagecreatefromwebp($path)
                ?: throw new \RuntimeException("Format gambar tidak didukung: {$mimeType}"),
        };
    }
}


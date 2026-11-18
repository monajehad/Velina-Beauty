<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

/**
 * Small JPEG copies of product photos for the catalog cards.
 * The original photo is kept for the zoom popup and the WhatsApp message.
 */
class ProductThumbnail
{
    public const DIR = 'products/thumbs';
    public const WIDTH = 500;
    public const QUALITY = 75;

    public static function pathFor(?string $imagePath): ?string
    {
        return $imagePath
            ? self::DIR . '/' . pathinfo($imagePath, PATHINFO_FILENAME) . '.jpg'
            : null;
    }

    public static function make(Product $product): bool
    {
        $disk = Storage::disk('public');
        $thumbPath = self::pathFor($product->image_path);
        if (! $thumbPath || ! $disk->exists($product->image_path)) {
            return false;
        }

        $src = $disk->path($product->image_path);
        $info = @getimagesize($src);
        if (! $info) {
            return false;
        }
        $type = $info[2];

        $img = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($src),
            IMAGETYPE_PNG => @imagecreatefrompng($src),
            IMAGETYPE_GIF => @imagecreatefromgif($src),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($src) : false,
            default => false,
        };
        if (! $img) {
            return false;
        }

        // Phone photos store rotation in EXIF; the resized copy drops EXIF, so apply it now.
        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $orientation = (int) (@exif_read_data($src)['Orientation'] ?? 1);
            $angle = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;
            if ($angle) {
                $rotated = imagerotate($img, $angle, 0);
                imagedestroy($img);
                $img = $rotated;
            }
        }

        $w = imagesx($img);
        $h = imagesy($img);
        $tw = min(self::WIDTH, $w);
        $th = (int) round($h * $tw / $w);

        $thumb = imagecreatetruecolor($tw, $th);
        imagefill($thumb, 0, 0, imagecolorallocate($thumb, 255, 255, 255)); // transparent PNG -> white
        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $tw, $th, $w, $h);

        $disk->makeDirectory(self::DIR);
        imageinterlace($thumb, true);
        $ok = imagejpeg($thumb, $disk->path($thumbPath), self::QUALITY);

        imagedestroy($img);
        imagedestroy($thumb);

        return $ok;
    }

    public static function delete(?string $imagePath): void
    {
        if ($thumbPath = self::pathFor($imagePath)) {
            Storage::disk('public')->delete($thumbPath);
        }
    }
}

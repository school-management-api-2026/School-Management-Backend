<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    public static function upload(?UploadedFile $file, string $folder = 'users'): ?string
    {
        if (!$file) {
            return null;
        }

        try {
            return $file->storeOnCloudinary($folder)->getSecureUrl();
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function delete(?string $imageUrl, string $folder = 'users'): void
    {
        if (!$imageUrl) {
            return;
        }

        try {
            $path = parse_url($imageUrl, PHP_URL_PATH);
            $pathWithoutExtension = pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME);

            $publicId = ltrim(strstr($pathWithoutExtension, $folder . '/'), '/');

            if ($publicId) {
                app('cloudinary')->uploadApi()->destroy($publicId);
            }
        } catch (\Throwable $th) {
            // Ignore deletion errors
        }
    }
}
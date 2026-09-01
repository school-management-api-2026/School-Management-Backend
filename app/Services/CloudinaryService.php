<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    public static function upload(
        ?UploadedFile $file,
        string $folder = 'users'
    ): ?string {

        if (!$file) {
            return null;
        }

        try {

            $uploadedFile = $file->storeOnCloudinary($folder);

            return $uploadedFile->getSecureUrl();

        } catch (\Throwable $th) {

            Log::error('Cloudinary upload failed', [
                'message' => $th->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);

            throw $th;
        }
    }

    public static function delete(
        ?string $imageUrl,
        string $folder = 'users'
    ): void {

        if (!$imageUrl) {
            return;
        }

        try {

            $path = parse_url($imageUrl, PHP_URL_PATH);

            $pathWithoutExtension =
                pathinfo($path, PATHINFO_DIRNAME)
                . '/'
                . pathinfo($path, PATHINFO_FILENAME);

            $publicId = ltrim(
                strstr($pathWithoutExtension, $folder . '/'),
                '/'
            );

            if ($publicId) {
                app('cloudinary')
                    ->uploadApi()
                    ->destroy($publicId);
            }

        } catch (\Throwable $th) {

            Log::error('Cloudinary delete failed', [
                'message' => $th->getMessage(),
                'image_url' => $imageUrl,
            ]);
        }
    }
}
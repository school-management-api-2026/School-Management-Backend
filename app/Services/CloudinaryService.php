<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CloudinaryService
{
    public static function upload($file, string $folder)
    {
        if (!$file) {
            return null;
        }

        $uploadedFile = Cloudinary::upload(
            $file->getRealPath(),
            [
                'folder' => $folder,
            ]
        );

        return $uploadedFile->getSecurePath();
    }

    public static function delete($imageUrl)
    {
        if (!$imageUrl) {
            return;
        }

        $publicId = self::extractPublicId($imageUrl);

        if (!$publicId) {
            return;
        }

        Cloudinary::destroy($publicId);
    }

    private static function extractPublicId(string $imageUrl)
    {
        if (!str_contains($imageUrl, '/image/upload/')) {
            return null;
        }

        $chunks = explode('/image/upload/', $imageUrl);

        $tail = end($chunks);

        $parts = explode('/', $tail);

        // Remove version: v123456
        if (isset($parts[0]) && preg_match('/^v\d+$/', $parts[0])) {
            array_shift($parts);
        }

        $tail = implode('/', $parts);

        // Remove extension
        $lastDot = strrpos($tail, '.');

        if ($lastDot !== false) {
            $tail = substr($tail, 0, $lastDot);
        }

        return $tail ?: null;
    }
}
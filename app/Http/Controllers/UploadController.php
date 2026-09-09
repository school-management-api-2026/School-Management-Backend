<?php

namespace App\Http\Controllers;

use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $url = CloudinaryService::upload($request->file('image'), 'profiles');

        if (!$url) {
            return response()->json([
                'message' => 'Upload failed',
            ], 422);
        }

        return response()->json([
            'message' => 'Uploaded successfully',
            'url' => $url,
        ], 200);
    }
}
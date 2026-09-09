<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display all settings as a key => value map.
     */
    public function index()
    {
        $settings = Settings::pluck('value', 'key');

        return response()->json([
            'message' => 'Get all settings successfully',
            'data' => $settings,
        ], 200);
    }

    /**
     * Upsert multiple settings at once.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            Settings::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        }

        $settings = Settings::pluck('value', 'key');

        return response()->json([
            'message' => 'Settings saved successfully',
            'data' => $settings,
        ], 200);
    }
}
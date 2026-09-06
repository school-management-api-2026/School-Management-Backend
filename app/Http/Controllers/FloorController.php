<?php

namespace App\Http\Controllers;

use App\Models\Floors;
use Illuminate\Http\Request;

class FloorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $floors = Floors::all();

        return response()->json([
            'message' => 'Get all floors successfully',
            'data' => $floors,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'floor_number' => 'required|integer',
            'building_id' => 'required|integer',
        ]);

        $floor = Floors::create($validated);

        return response()->json([
            'message' => 'Created floor successfully',
            'data' => $floor,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Floors $floor)
    {
        return response()->json([
            'message' => 'Get floor by id successfully',
            'data' => $floor,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Floors $floor)
    {
        $validated = $request->validate([
            'floor_number' => 'required|integer',
            'building_id' => 'required|integer',
        ]);

        $floor->update($validated);

        return response()->json([
            'message' => 'Updated floor successfully',
            'data' => $floor,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Floors $floor)
    {
        $floor->delete();

        return response()->json([
            'message' => 'Delete floor successfully',
        ], 200);
    }
}
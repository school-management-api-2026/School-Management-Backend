<?php

namespace App\Http\Controllers;

use App\Models\Rooms;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Rooms::with('floor.building')->get();

        return response()->json([
            'message' => 'Get all rooms successfully',
            'data' => $rooms,
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
            'room_number' => 'required|string|max:50',
            'room_type' => 'required|string|max:50',
            'capacity' => 'required|integer',
            'floor_id' => 'required|integer',
        ]);

        $room = Rooms::create($validated);

        return response()->json([
            'message' => 'Created room successfully',
            'data' => $room,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Rooms $room)
    {
        $room->load('floor.building');

        return response()->json([
            'message' => 'Get room by id successfully',
            'data' => $room,
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
    public function update(Request $request, Rooms $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:50',
            'room_type' => 'required|string|max:50',
            'capacity' => 'required|integer',
            'floor_id' => 'required|integer',
        ]);

        $room->update($validated);

        return response()->json([
            'message' => 'Updated room successfully',
            'data' => $room,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rooms $room)
    {
        $room->delete();

        return response()->json([
            'message' => 'Delete room successfully',
        ], 200);
    }
}
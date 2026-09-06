<?php

namespace App\Http\Controllers;

use App\Models\Attendances;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendances = Attendances::all();

        return response()->json([
            'message' => 'Get all attendances successfully',
            'data' => $attendances,
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
            'date' => 'required|date',
            'time_in' => 'nullable',
            'time_out' => 'nullable',
            'status' => 'required|string|max:30',
            'user_id' => 'required|integer',
        ]);

        $attendance = Attendances::create($validated);

        return response()->json([
            'message' => 'Created attendance successfully',
            'data' => $attendance,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendances $attendance)
    {
        return response()->json([
            'message' => 'Get attendance by id successfully',
            'data' => $attendance,
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
    public function update(Request $request, Attendances $attendance)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time_in' => 'nullable',
            'time_out' => 'nullable',
            'status' => 'required|string|max:30',
            'user_id' => 'required|integer',
        ]);

        $attendance->update($validated);

        return response()->json([
            'message' => 'Updated attendance successfully',
            'data' => $attendance,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendances $attendance)
    {
        $attendance->delete();

        return response()->json([
            'message' => 'Delete attendance successfully',
        ], 200);
    }
}
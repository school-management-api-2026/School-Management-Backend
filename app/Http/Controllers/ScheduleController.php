<?php

namespace App\Http\Controllers;

use App\Models\Schedules;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = Schedules::all();

        return response()->json([
            'message' => 'Get all schedules successfully',
            'data' => $schedules,
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
            'day_of_week' => 'required|string|max:20',
            'time_start' => 'required',
            'time_end' => 'required',
            'room_id' => 'required|integer',
            'teacher_course_id' => 'required|integer',
        ]);

        $schedule = Schedules::create($validated);

        return response()->json([
            'message' => 'Created schedule successfully',
            'data' => $schedule,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedules $schedule)
    {
        return response()->json([
            'message' => 'Get schedule by id successfully',
            'data' => $schedule,
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
    public function update(Request $request, Schedules $schedule)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|string|max:20',
            'time_start' => 'required',
            'time_end' => 'required',
            'room_id' => 'required|integer',
            'teacher_course_id' => 'required|integer',
        ]);

        $schedule->update($validated);

        return response()->json([
            'message' => 'Updated schedule successfully',
            'data' => $schedule,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedules $schedule)
    {
        $schedule->delete();

        return response()->json([
            'message' => 'Delete schedule successfully',
        ], 200);
    }
}
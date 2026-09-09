<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Courses::with('subject')->get();

        return response()->json([
            'message' => 'Get all courses successfully',
            'data' => $courses,
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
            'unit_price' => 'required|numeric',
            'promotion' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'capacity' => 'required|integer',
            'subject_id' => 'required|integer',
        ]);

        $course = Courses::create($validated);

        return response()->json([
            'message' => 'Created course successfully',
            'data' => $course,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Courses $course)
    {
        $course->load('subject');

            return response()->json([
            'message' => 'Get course by id successfully',
            'data' => $course,
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
    public function update(Request $request, Courses $course)
    {
        $validated = $request->validate([
            'unit_price' => 'required|numeric',
            'promotion' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'capacity' => 'required|integer',
            'subject_id' => 'required|integer',
        ]);

        $course->update($validated);

        return response()->json([
            'message' => 'Updated course successfully',
            'data' => $course,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Courses $course)
    {
        $course->delete();

        return response()->json([
            'message' => 'Delete course successfully',
        ], 200);
    }
}
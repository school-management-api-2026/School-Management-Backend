<?php

namespace App\Http\Controllers;

use App\Models\Enrollments;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enrollments = Enrollments::all();

        return response()->json([
            'message' => 'Get all enrollments successfully',
            'data' => $enrollments,
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
            'enrollment_date' => 'required|date',
            'status' => 'required|string',
            'student_id' => 'required|integer',
            'course_id' => 'required|integer',
        ]);

        $enrollment = Enrollments::create($validated);

        return response()->json([
            'message' => 'Created enrollment successfully',
            'data' => $enrollment,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollments $enrollment)
    {
        return response()->json([
            'message' => 'Get enrollment by id successfully',
            'data' => $enrollment,
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
    public function update(Request $request, Enrollments $enrollment)
    {
        $validated = $request->validate([
            'enrollment_date' => 'required|date',
            'status' => 'required|string',
            'student_id' => 'required|integer',
            'course_id' => 'required|integer',
        ]);

        $enrollment->update($validated);

        return response()->json([
            'message' => 'Updated enrollment successfully',
            'data' => $enrollment,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollments $enrollment)
    {
        $enrollment->delete();

        return response()->json([
            'message' => 'Delete enrollment successfully',
        ], 200);
    }
}
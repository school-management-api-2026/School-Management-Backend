<?php

namespace App\Http\Controllers;

use App\Models\Exams;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exams = Exams::with('subject', 'teacher.user', 'course.subject')->get();

        return response()->json([
            'message' => 'Get all exams successfully',
            'data' => $exams,
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
            'exam_date' => 'required|date',
            'exam_type' => 'required|string|max:50',
            'course_id' => 'required|integer',
            'teacher_id' => 'required|integer',
            'subject_id' => 'required|integer',
        ]);

        $exam = Exams::create($validated);

        return response()->json([
            'message' => 'Created exam successfully',
            'data' => $exam,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Exams $exam)
    {
        $exam->load('subject', 'teacher.user', 'course.subject');

        return response()->json([
            'message' => 'Get exam by id successfully',
            'data' => $exam,
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
    public function update(Request $request, Exams $exam)
    {
        $validated = $request->validate([
            'exam_date' => 'required|date',
            'exam_type' => 'required|string|max:50',
            'course_id' => 'required|integer',
            'teacher_id' => 'required|integer',
            'subject_id' => 'required|integer',
        ]);

        $exam->update($validated);

        return response()->json([
            'message' => 'Updated exam successfully',
            'data' => $exam,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exams $exam)
    {
        $exam->delete();

        return response()->json([
            'message' => 'Delete exam successfully',
        ], 200);
    }
}
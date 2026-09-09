<?php

namespace App\Http\Controllers;

use App\Models\Results;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $results = Results::with('enrollment.student.user', 'enrollment.course.subject', 'exam.subject')->get();

        return response()->json([
            'message' => 'Get all results successfully',
            'data' => $results,
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
            'score' => 'required|numeric',
            'grade' => 'required|string|max:10',
            'enrollment_id' => 'required|integer',
            'exam_id' => 'required|integer',
        ]);

        $result = Results::create($validated);

        return response()->json([
            'message' => 'Created result successfully',
            'data' => $result,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Results $result)
    {
        $result->load('enrollment.student.user', 'enrollment.course.subject', 'exam.subject');

        return response()->json([
            'message' => 'Get result by id successfully',
            'data' => $result,
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
    public function update(Request $request, Results $result)
    {
        $validated = $request->validate([
            'score' => 'required|numeric',
            'grade' => 'required|string|max:10',
            'enrollment_id' => 'required|integer',
            'exam_id' => 'required|integer',
        ]);

        $result->update($validated);

        return response()->json([
            'message' => 'Updated result successfully',
            'data' => $result,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Results $result)
    {
        $result->delete();

        return response()->json([
            'message' => 'Delete result successfully',
        ], 200);
    }
}
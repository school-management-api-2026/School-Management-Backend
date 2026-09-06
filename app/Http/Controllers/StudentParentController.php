<?php

namespace App\Http\Controllers;

use App\Models\StudentParents;
use Illuminate\Http\Request;

class StudentParentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $studentParents = StudentParents::all();

        return response()->json([
            'message' => 'Get all student parents successfully',
            'data' => $studentParents,
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
            'relation' => 'required|string|max:50',
            'is_primary' => 'required|boolean',
            'student_id' => 'required|integer',
            'parent_id' => 'required|integer',
        ]);

        $studentParent = StudentParents::create($validated);

        return response()->json([
            'message' => 'Created student parent successfully',
            'data' => $studentParent,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentParents $studentParent)
    {
        return response()->json([
            'message' => 'Get student parent by id successfully',
            'data' => $studentParent,
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
    public function update(Request $request, StudentParents $studentParent)
    {
        $validated = $request->validate([
            'relation' => 'required|string|max:50',
            'is_primary' => 'required|boolean',
            'student_id' => 'required|integer',
            'parent_id' => 'required|integer',
        ]);

        $studentParent->update($validated);

        return response()->json([
            'message' => 'Updated student parent successfully',
            'data' => $studentParent,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentParents $studentParent)
    {
        $studentParent->delete();

        return response()->json([
            'message' => 'Delete student parent successfully',
        ], 200);
    }
}
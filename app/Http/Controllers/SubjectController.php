<?php

namespace App\Http\Controllers;

use App\Models\Subjects;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subjects::all();
        return response()->json([
            'message' => 'Get all subject successfully',
            'data' => $subjects,
        ],200);
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
            'name' => 'required|string|max:100',
            'description' => 'nullable|text',
        ]);

        $subject = Subjects::create($validated);

        return response()->json([
            'message' => 'Created subject successfully.',
            'data' => $subject,
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Subjects $subject)
    {
        return response()->json([
            'message' => 'Get subject by id successfully',
            'data' => $subject,
        ],200);
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
    public function update(Request $request, Subjects $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|text',
        ]);

        $subject -> update($validated);

        return response()->json([
            'message' => 'Updated subject successfully',
            'data' => $subject,
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subjects $subject)
    {
        $subject->delete();

        return response()->json([
            'message' => 'Delete subject successfully',
        ],200);
    }
}

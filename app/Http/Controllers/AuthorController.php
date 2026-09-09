<?php

namespace App\Http\Controllers;

use App\Models\Authors;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authors = Authors::with('book_authors.book')->get();

        return response()->json([
            'message' => 'Get all authors successfully',
            'data' => $authors,
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
            'name' => 'required|string|max:255',
            'gender' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date',
            'nation' => 'required|string|max:255',
        ]);

        $author = Authors::create($validated);

        return response()->json([
            'message' => 'Created author successfully',
            'data' => $author,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $author = Authors::with('book_authors.book')->findOrFail($id);

        return response()->json([
            'message' => 'Get author by id successfully',
            'data' => $author,
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
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date',
            'nation' => 'required|string|max:255',
        ]);

        $author = Authors::findOrFail($id);
        $author->update($validated);

        return response()->json([
            'message' => 'Updated author successfully',
            'data' => $author,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $author = Authors::findOrFail($id);
        $author->delete();

        return response()->json([
            'message' => 'Delete author successfully',
        ], 200);
    }
}
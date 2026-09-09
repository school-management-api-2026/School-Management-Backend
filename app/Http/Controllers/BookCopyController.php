<?php

namespace App\Http\Controllers;

use App\Models\BookCopies;
use Illuminate\Http\Request;

class BookCopyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $copies = BookCopies::with('book')->get();

        return response()->json([
            'message' => 'Get all book copies successfully',
            'data' => $copies,
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
            'barcode' => 'required|string|max:100|unique:book_copies,barcode',
            'status' => 'required|string|max:20',
            'book_id' => 'required|integer',
        ]);

        $copy = BookCopies::create($validated);

        $copy->load('book');

        return response()->json([
            'message' => 'Created book copy successfully',
            'data' => $copy,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $copy = BookCopies::with('book')->findOrFail($id);

        return response()->json([
            'message' => 'Get book copy by id successfully',
            'data' => $copy,
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
            'barcode' => 'required|string|max:100|unique:book_copies,barcode,' . $id,
            'status' => 'required|string|max:20',
            'book_id' => 'required|integer',
        ]);

        $copy = BookCopies::findOrFail($id);
        $copy->update($validated);

        $copy->load('book');

        return response()->json([
            'message' => 'Updated book copy successfully',
            'data' => $copy,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $copy = BookCopies::findOrFail($id);
        $copy->delete();

        return response()->json([
            'message' => 'Delete book copy successfully',
        ], 200);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\BookLoans;
use Illuminate\Http\Request;

class BookLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $loans = BookLoans::with('borrower', 'bookCopy.book', 'staff')->get();

        return response()->json([
            'message' => 'Get all book loans successfully',
            'data' => $loans,
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
            'loan_date' => 'required|date',
            'due_date' => 'required|date',
            'return_date' => 'nullable|date',
            'status' => 'required|string|max:20',
            'user_id' => 'required|integer',
            'book_copy_id' => 'required|integer',
            'library_staff_id' => 'required|integer',
        ]);

        $loan = BookLoans::create($validated);

        $loan->load('borrower', 'bookCopy.book', 'staff');

        return response()->json([
            'message' => 'Created book loan successfully',
            'data' => $loan,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $loan = BookLoans::with('borrower', 'bookCopy.book', 'staff')->findOrFail($id);

        return response()->json([
            'message' => 'Get book loan by id successfully',
            'data' => $loan,
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
            'loan_date' => 'required|date',
            'due_date' => 'required|date',
            'return_date' => 'nullable|date',
            'status' => 'required|string|max:20',
            'user_id' => 'required|integer',
            'book_copy_id' => 'required|integer',
            'library_staff_id' => 'required|integer',
        ]);

        $loan = BookLoans::findOrFail($id);
        $loan->update($validated);

        $loan->load('borrower', 'bookCopy.book', 'staff');

        return response()->json([
            'message' => 'Updated book loan successfully',
            'data' => $loan,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $loan = BookLoans::findOrFail($id);
        $loan->delete();

        return response()->json([
            'message' => 'Delete book loan successfully',
        ], 200);
    }
}
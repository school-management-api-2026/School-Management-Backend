<?php

namespace App\Http\Controllers;

use App\Models\Fines;
use Illuminate\Http\Request;

class FineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fines = Fines::with('book_loan.borrower', 'book_loan.bookCopy.book')->get();

        return response()->json([
            'message' => 'Get all fines successfully',
            'data' => $fines,
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
            'amount' => 'required|numeric|min:0',
            'paid_status' => 'required|string|max:20',
            'book_loan_id' => 'required|integer',
        ]);

        $fine = Fines::create($validated);

        $fine->load('book_loan.borrower', 'book_loan.bookCopy.book');

        return response()->json([
            'message' => 'Created fine successfully',
            'data' => $fine,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $fine = Fines::with('book_loan.borrower', 'book_loan.bookCopy.book')->findOrFail($id);

        return response()->json([
            'message' => 'Get fine by id successfully',
            'data' => $fine,
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
            'amount' => 'required|numeric|min:0',
            'paid_status' => 'required|string|max:20',
            'book_loan_id' => 'required|integer',
        ]);

        $fine = Fines::findOrFail($id);
        $fine->update($validated);

        $fine->load('book_loan.borrower', 'book_loan.bookCopy.book');

        return response()->json([
            'message' => 'Updated fine successfully',
            'data' => $fine,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fine = Fines::findOrFail($id);
        $fine->delete();

        return response()->json([
            'message' => 'Delete fine successfully',
        ], 200);
    }
}
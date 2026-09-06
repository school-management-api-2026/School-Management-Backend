<?php

namespace App\Http\Controllers;

use App\Models\Invoices;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoices::with('payments')->get();

        return response()->json([
            'message' => 'Get all invoices successfully',
            'data' => $invoices,
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
            'total_amount' => 'required|numeric',
            'status' => 'required|string',
            'due_date' => 'required|date',
            'enrollment_id' => 'required|integer',
        ]);

        $invoice = Invoices::create($validated);

        return response()->json([
            'message' => 'Created invoice successfully',
            'data' => $invoice,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoices $invoice)
    {
        $invoice->load('payments');

        return response()->json([
            'message' => 'Get invoice by id successfully',
            'data' => $invoice,
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
    public function update(Request $request, Invoices $invoice)
    {
        $validated = $request->validate([
            'total_amount' => 'required|numeric',
            'status' => 'required|string',
            'due_date' => 'required|date',
            'enrollment_id' => 'required|integer',
        ]);

        $invoice->update($validated);

        return response()->json([
            'message' => 'Updated invoice successfully',
            'data' => $invoice,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoices $invoice)
    {
        $invoice->delete();

        return response()->json([
            'message' => 'Delete invoice successfully',
        ], 200);
    }
}
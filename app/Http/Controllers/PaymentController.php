<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payments::all();

        return response()->json([
            'message' => 'Get all payments successfully',
            'data' => $payments,
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
            'amount_paid' => 'required|numeric',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'status' => 'required|string',
            'invoice_id' => 'required|integer',
        ]);

        $payment = Payments::create($validated);

        return response()->json([
            'message' => 'Created payment successfully',
            'data' => $payment,
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payments $payment)
    {
        $payment->load('invoice');

        return response()->json([
            'message' => 'Get payment by id successfully',
            'data' => $payment,
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
    public function update(Request $request, Payments $payment)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'status' => 'required|string',
            'invoice_id' => 'required|integer',
        ]);

        $payment->update($validated);

        return response()->json([
            'message' => 'Updated payment successfully',
            'data' => $payment,
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payments $payment)
    {
        $payment->delete();

        return response()->json([
            'message' => 'Delete payment successfully',
        ],200);  
    }
}

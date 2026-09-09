<?php

namespace App\Http\Controllers;

use App\Models\Payrolls;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payrolls = Payrolls::with('teacher.user')->get();

        return response()->json([
            'message' => 'Get all payrolls successfully',
            'data' => $payrolls,
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
            'base_salary' => 'required|numeric',
            'bonus' => 'nullable|numeric',
            'deduction' => 'nullable|numeric',
            'net_salary' => 'required|numeric',
            'pay_date' => 'required|date',
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after_or_equal:pay_period_start',
            'teacher_id' => 'required|integer',
        ]);

        $payroll = Payrolls::create($validated);

        return response()->json([
            'message' => 'Created payroll successfully',
            'data' => $payroll,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payrolls $payroll)
    {
        $payroll->load('teacher.user');

        return response()->json([
            'message' => 'Get payroll by id successfully',
            'data' => $payroll,
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
    public function update(Request $request, Payrolls $payroll)
    {
        $validated = $request->validate([
            'base_salary' => 'required|numeric',
            'bonus' => 'nullable|numeric',
            'deduction' => 'nullable|numeric',
            'net_salary' => 'required|numeric',
            'pay_date' => 'required|date',
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after_or_equal:pay_period_start',
            'teacher_id' => 'required|integer',
        ]);

        $payroll->update($validated);

        return response()->json([
            'message' => 'Updated payroll successfully',
            'data' => $payroll,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payrolls $payroll)
    {
        $payroll->delete();

        return response()->json([
            'message' => 'Delete payroll successfully',
        ], 200);
    }
}
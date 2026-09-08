<?php

namespace App\Http\Controllers;

use App\Models\Buildings;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $buidings = Buildings::with('floors')->get();

        return response()->json([
            'message' => 'Get all buildings successfully',
            'data' => $buidings,
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
            'name' => 'required|string|max:150',
            'total_floors' => 'required|integer',
            'description' => 'nullable|string', 
            'status' => 'required',
        ]);

        $building = Buildings::create($validated);

        return response()->json([
            'message' => 'Created building successfully',
            'data' => $building,
        ],201);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Buildings $building)
    {
        $building->load('floors');

        return response()->json([
            'message' => 'Get building by id successfully',
            'data' => $building,
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
    public function update(Request $request, Buildings $building)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'total_floors' => 'required|integer',
            'description' => 'nullable|string', 
            'status' => 'required',
        ]);

        $hasChange = false;
        foreach ($validated as $key => $value) {
            if (isset($value) && $building->{$key} != $value) {
                $hasChange = true;
                break;
            }
        }

        if (!$hasChange) {
            return response()->json([
                'message' => 'Nothing building update',
            ], 200);
        }

        $building->update($validated);

        return response()->json([
            'message' => 'Updated building successfully',
            'data' => $building->load('floors'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Buildings $building)
    {
        $building->delete();

        return response()->json([
            'message' => 'Delete building successfully',
        ],200);
    }
}

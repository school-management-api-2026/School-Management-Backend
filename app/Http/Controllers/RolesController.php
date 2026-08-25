<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Roles::all();

        return response()->json([
            'message'=>'Get all roles',
            'data'=>$roles,
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
            'name'=>'required|string|unique:roles,name',
        ]);

        $role = Roles::create($validated);

        return response()->json([
            'message'=>'Created role successfully.',
            'data'=>$role,
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Roles $roles)
    {
        return response()->json([
            'message'=>'Get role by id.',
            'data'=>$roles
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Roles $roles)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Roles $roles)
    {
        $validated = $request->validate([
            'name'=>'required|string|unique:roles,name'.$roles->id,
        ]);
        
        $roles->update($validated);
        
        return response()->json([
            'message'=>'Updated role successfully.',
            'data'=>$roles,
        ],202);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Roles $roles)
    {
        $roles->delete();
        return response()->json([
            'message'=>'Delete role successfully.',
        ],200);
    }
}

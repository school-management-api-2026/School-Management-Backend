<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parents = Parents::with('user')->get();

        return response()->json([
            'message' => 'Get all parents successfully',
            'data' => $parents,
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
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $year = date('Y');
            

            $latestUser = User::lockForUpdate()->latest()->first();
            $nextId = $latestUser ? $latestUser->id + 1 : 1;
            $validated['code'] = 'USR-' . $year . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

            if ($request->hasFile('image')) {

                logger('Image exists');

                logger([
                    'file' => $request->file('image')->getClientOriginalName(),
                    'path' => $request->file('image')->getRealPath(),
                    'valid' => $request->file('image')->isValid(),
                ]);

                $validated['image'] = CloudinaryService::upload(
                    $request->file('image'),
                    'parents'
                );
            }

            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);

            $parent = Parents::create([
                'user_id' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Created parent successfully.',
                'data' => $parent->load('user'),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create parent',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Parents $parent)
    {
        $parent->load('user');

        return response()->json([
            'message' => 'Get parent by id successfully',
            'data' => $parent,
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
    public function update(Request $request, Parents $parent)
    {
        $user = $parent->user;
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $userData = [];
            foreach (['name', 'username', 'email', 'phone', 'gender', 'date_of_birth', 'role_id'] as $field) {
                if ($request->has($field)) {
                    $userData[$field] = $validated[$field];
                }
            }

            if ($request->hasFile('image')) {
                CloudinaryService::delete($user->image, 'parents');
                $userData['image'] = CloudinaryService::upload($request->file('image'), 'parents');
            }

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            if (!empty($userData)) {
                $user->update($userData);
            }

            DB::commit();

            return response()->json([
                'message' => 'Update parent successfully',
                'data' => $parent->load('user'),
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update parent',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Parents $parent)
    {
         DB::beginTransaction();
        try {
            $user = $parent->user;
            
            if ($user && $user->image) {
                CloudinaryService::delete($user->image, 'parents');
            }

            $parent->delete();

            if ($user) {
                $user->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Delete parent and user successfully'
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete parent',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
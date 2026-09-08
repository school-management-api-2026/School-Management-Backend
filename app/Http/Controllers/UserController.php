<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $users = User::with('role')->get()->map(function ($user) {
        $user->role_name = $user->role->name ?? '—';
        return $user;
    });

    return response()->json([
        'message' => 'Get all users',
        'data' => $users,
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
    public function store(StoreUserRequest $request)
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
                    'users'
                );
            }

            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user = User::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'Created user successfully with auto code.',
                'data' => $user,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('role');

        return response()->json([
            'message' => 'Get user detail successfully',
            'data' => $user,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUserRequest $request, User $user)
    {
        $validated = $request->validated();
            
        if ($request->hasFile('image')) {
            CloudinaryService::delete($user->image, 'users');
            $validated['image'] = CloudinaryService::upload($request->file('image'), 'users');
        }
            
        if (!empty($validated['password'])) {
            if (Hash::check($request->password, $user->password)) {
                unset($validated['password']); 
            } else {
                $validated['password'] = Hash::make($validated['password']);
            }
        } else {
            unset($validated['password']);
        }

        $hasChange = false;
        foreach ($validated as $key => $value) {
            if ($user->{$key} != $value) {
                $hasChange = true;
                break;
            }
        }

        if (!$hasChange) {
            return response()->json([
                'message' => 'Nothing user update',
            ]);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Update user successfuly',
            'data' => $user,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        CloudinaryService::delete($user->image, 'users');

        $user->delete();

        return response()->json([
            'message' => 'Delete user and image from Cloudinary successfully'
        ], 200);
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Teachers;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teachers::with('user.role')->get();

        return response()->json([

            'message' => 'Get all teacher successfully',
            'data' => $teachers,
            
        ], 200);
    }

    public function create()
    {
        //
    }

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
                    'teachers'
                );
            }

            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user = User::create($validated);

            $teacher = Teachers::create([
                'user_id' => $user->id,
                'hire_date' => $validated['hire_date'] ?? $request->input('hire_date'),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Created teacher successfully.',
                'data' => $teacher->load('user'),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create teacher',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Teachers $teacher)
    {
        $teacher->load('user.role');
        
        return response()->json([
            'message' => 'Get teacher detail successfully',
            'data' => $teacher,
        ], 200);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(StoreUserRequest $request, Teachers $teacher)
    {
        $user = $teacher->user;

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
                CloudinaryService::delete($user->image, 'teachers');
                $userData['image'] = CloudinaryService::upload($request->file('image'), 'teachers');
            } elseif ($request->filled('image')) {
                if ($user->image && $user->image !== $validated['image']) {
                    CloudinaryService::delete($user->image, 'teachers');
                }
                $userData['image'] = $validated['image'];
            }

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            if (!empty($userData)) {
                $user->update($userData);
            }

            if ($request->has('hire_date')) {
                $teacher->update([
                    'hire_date' => $request->input('hire_date')
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Update teacher successfully',
                'data' => $teacher->load('user'),
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update teacher',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Teachers $teacher)
    {
        DB::beginTransaction();
        try {
            $user = $teacher->user;

            if($user && $user->image) {

                CloudinaryService::delete($user->image, 'teachers');
            }

            $teacher->delete();
            if ($user) {
                $user->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Delete teacher and user successfully'
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete teacher',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

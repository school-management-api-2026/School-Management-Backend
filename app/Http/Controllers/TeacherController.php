<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teachers;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teachers::with('user')->get();
        return response()->json([
            'message' => 'Get all teacher successfully',
            'data' => $teachers,
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(StoreTeacherRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'] ?? null,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'role_id' => $validated['role_id'],
            ]);

            $teacher = Teachers::create([
                'hire_date' => $validated['hire_date'],
                'user_id' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Created teacher successfully',
                'data' => [
                    'teacher' => $teacher->load('user'),
                ],
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
        $teacher->load('user');
        
        return response()->json([
            'message' => 'Get teacher detail successfully',
            'data' => $teacher,
        ], 200);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(UpdateTeacherRequest $request, Teachers $teacher)
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

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            if (!empty($userData)) {
                $user->update($userData);
            }

            if ($request->has('hire_date')) {
                $teacher->update(['hire_date' => $validated['hire_date']]);
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

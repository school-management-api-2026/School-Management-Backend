<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Students;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $students = Students::with('user.role')->get(); 

        return response()->json([
            'message' => 'Get all students successfully',
            'data' => $students,
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
                $validated['image'] = CloudinaryService::upload($request->file('image'), 'students');
            }

            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);

            $student = Students::create([
                'user_id' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Created student successfully.',
                'data' => $student->load('user'),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Students $student)
    {
        $student->load('user.role');

        return response()->json([
            'message' => 'Get student detail successfully',
            'data' => $student,
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
    public function update(StoreUserRequest $request, Students $student)
    {
        $user = $student->user;
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
                CloudinaryService::delete($user->image, 'students');
                $userData['image'] = CloudinaryService::upload($request->file('image'), 'students');
            }

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            if (!empty($userData)) {
                $user->update($userData);
            }

            DB::commit();

            return response()->json([
                'message' => 'Update student successfully',
                'data' => $student->load('user'),
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Students $student)
    {
        DB::beginTransaction();
        try {
            $user = $student->user;
            
            if ($user && $user->image) {
                CloudinaryService::delete($user->image, 'students');
            }

            $student->delete();

            if ($user) {
                $user->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Delete student and user successfully'
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete student',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Students;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $students = Students::with('user')->get();

        return response()->json([
            'message' => 'Get all student successfully',
            'data' => $students,
        ],201);
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
    public function store(StoreStudentRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {

            $year = date('Y');
            
            $latestStudent = Students::lockForUpdate()->latest()->first();
            $nextId = $latestStudent ? $latestStudent->id + 1 : 1;
            $studentCode = 'ST-' . $year . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

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

            // B. បង្កើត Student ជាមួយ Code
            $student = Students::create([
                'code' => $studentCode,
                'user_id' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Created student safely with lock mechanism',
                'data' => [
                    'student' => $student,
                    'user' => $user
                ],
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

        $student->load('user');

        return response()->json([

            'message' => 'Get student detail successfully',
            'data' => $student,
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
    public function update(UpdateStudentRequest $request, Students $student)
    {
        $user = $student->user;

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Update User data if exists in request
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

            // Update Student data (like code) if provided
            if ($request->has('code')) {
                $student->update(['code' => $validated['code']]);
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
            
            // delete student before user
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

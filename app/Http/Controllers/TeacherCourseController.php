<?php

namespace App\Http\Controllers;

use App\Models\TeacherCourses;
use Illuminate\Http\Request;

class TeacherCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teacherCourses = TeacherCourses::with('teacher.user', 'course.subject')->get();

        return response()->json([
            'message' => 'Get all teacher courses successfully',
            'data' => $teacherCourses,
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
            'teacher_id' => 'required|integer',
            'course_id' => 'required|integer',
        ]);

        $teacherCourse = TeacherCourses::create($validated);

        return response()->json([
            'message' => 'Created teacher course successfully',
            'data' => $teacherCourse,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TeacherCourses $teacherCourse)
    {
        return response()->json([
            'message' => 'Get teacher course by id successfully',
            'data' => $teacherCourse,
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
    public function update(Request $request, TeacherCourses $teacherCourse)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|integer',
            'course_id' => 'required|integer',
        ]);

        $teacherCourse->update($validated);

        return response()->json([
            'message' => 'Updated teacher course successfully',
            'data' => $teacherCourse,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeacherCourses $teacherCourse)
    {
        $teacherCourse->delete();

        return response()->json([
            'message' => 'Delete teacher course successfully',
        ], 200);
    }
}
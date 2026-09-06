<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('/user', UserController::class);//only admin

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/role', RoleController::class)->middleware('role:1');
    Route::apiResource('/student', StudentController::class)->middleware('role:1');
    Route::apiResource('/teacher', TeacherController::class)->middleware('role:1');
    Route::apiResource('/subject', SubjectController::class)->middleware('role:1');
    Route::apiResource('/building', BuildingController::class)->middleware('role:1');
    Route::apiResource('/payment', PaymentController::class)->middleware('role:1');
    Route::apiResource('/enrollment', EnrollmentController::class)->middleware('role:1');
    Route::apiResource('/exam', ExamController::class)->middleware('role:1');
    Route::apiResource('/course', CourseController::class)->middleware('role:1');
    Route::apiResource('/invoice', InvoiceController::class)->middleware('role:1');
    Route::apiResource('/result', ResultController::class)->middleware('role:1');
    Route::apiResource('/floor', FloorController::class)->middleware('role:1');
    Route::apiResource('/parent', ParentController::class)->middleware('role:1');
    Route::apiResource('/payroll', PayrollController::class)->middleware('role:1');
    Route::apiResource('/room', RoomController::class)->middleware('role:1');
    Route::apiResource('/schedule', ScheduleController::class)->middleware('role:1');
    Route::apiResource('/attendance', AttendanceController::class)->middleware('role:1');
});


<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\RoleController;
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
    Route::apiResource('/subject', SubjectController::class)->middleware('role:1');
    Route::apiResource('/building', BuildingController::class)->middleware('role:1');
});


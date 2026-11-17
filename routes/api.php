<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LevelController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\ClassroomController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\ScheduleEntryController;
use App\Http\Controllers\Api\TeacherScheduleController;


Route::post('users', [UserController::class, 'store']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/levels', [LevelController::class, 'index']);
    Route::post('/levels', [LevelController::class, 'store']);
    Route::get('/levels/{level}', [LevelController::class, 'show']);
    Route::put('/levels/{level}', [LevelController::class, 'update']);
    Route::delete('/levels/{level}', [LevelController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::post('/subjects', [SubjectController::class, 'store']);
    Route::get('/subjects/{subject}', [SubjectController::class, 'show']);
    Route::put('/subjects/{subject}', [SubjectController::class, 'update']);
    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/classrooms', [ClassroomController::class, 'index']);
    Route::post('/classrooms', [ClassroomController::class, 'store']);
    Route::get('/classrooms/{classroom}', [ClassroomController::class, 'show']);
    Route::put('/classrooms/{classroom}', [ClassroomController::class, 'update']);
    Route::delete('/classrooms/{classroom}', [ClassroomController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/students', [StudentController::class, 'index']);
    Route::post('/students', [StudentController::class, 'store']);
    Route::get('/students/{student}', [StudentController::class, 'show']);
    Route::put('/students/{student}', [StudentController::class, 'update']);
    Route::delete('/students/{student}', [StudentController::class, 'destroy']);
    Route::post('/students/{student}/classrooms', [StudentController::class, 'assignClassroom']);
});
 Route::middleware('auth:sanctum')->group(function () {
    Route::get('/teachers', [TeacherController::class, 'index']);
    Route::post('/teachers', [TeacherController::class, 'store']);
    Route::get('/teachers/{teacher}', [TeacherController::class, 'show']);
    Route::put('/teachers/{teacher}', [TeacherController::class, 'update']);
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy']);
    Route::put('/teachers/{teacher}/subjects', [TeacherController::class, 'syncSubjects']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/schedule-entries', [ScheduleEntryController::class, 'index']);
    Route::post('/schedule-entries', [ScheduleEntryController::class, 'store']);
    Route::get('/schedule-entries/{scheduleEntry}', [ScheduleEntryController::class, 'show']);
    Route::put('/schedule-entries/{scheduleEntry}', [ScheduleEntryController::class, 'update']);
    Route::delete('/schedule-entries/{scheduleEntry}', [ScheduleEntryController::class, 'destroy']);
});

Route::get('teachers/{teacher}/schedule', TeacherScheduleController::class);

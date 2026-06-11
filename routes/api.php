<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

// User Route
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

    // Get all tasks
    Route::get('/tasks', [TaskController::class, 'index']);

    // Create new task
    Route::post('/tasks', [TaskController::class, 'store']);

    // Update task (DONE toggle)
    Route::patch('/tasks/{id}', [TaskController::class, 'update']);

    // Delete task
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);

});
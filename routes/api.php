<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaskDependencyController;

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

// Public routes (no authentication required)
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // Authentication routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Task routes
    Route::apiResource('tasks', TaskController::class);
    
    // Task dependency routes
    Route::post('/task-dependencies', [TaskDependencyController::class, 'store']);
    Route::get('/tasks/{taskId}/dependencies', [TaskDependencyController::class, 'show']);
    Route::delete('/task-dependencies/{id}', [TaskDependencyController::class, 'destroy']);
});
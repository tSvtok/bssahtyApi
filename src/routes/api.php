<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\SpotController;
use App\Http\Controllers\Api\SportCategoryController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\ResponseController;
use Illuminate\Support\Facades\Hash;

// Public API routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});
Route::get('/test', function () {
    return response()->json(['password' => Hash::make('password')]);
});

Route::get('sport-categories', [SportCategoryController::class, 'index']);
Route::get('spots', [SpotController::class, 'index']);
Route::get('spots/nearby', [SpotController::class, 'nearby']);
Route::get('spots/{spot}', [SpotController::class, 'show']);
Route::get('questions', [QuestionController::class, 'index']);
Route::get('questions/{question}', [QuestionController::class, 'show']);
Route::get('events', [EventController::class, 'index']);
Route::get('events/{event}', [EventController::class, 'show']);

// Authenticated API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/user', [AuthController::class, 'user']);
    Route::put('auth/profile', [AuthController::class, 'updateProfile']);

    Route::post('spots', [SpotController::class, 'store']);
    Route::put('spots/{spot}', [SpotController::class, 'update']);
    Route::delete('spots/{spot}', [SpotController::class, 'destroy']);
    Route::post('spots/{spot}/favorite', [SpotController::class, 'toggleFavorite']);
    Route::post('spots/{spot}/reviews', [SpotController::class, 'addReview']);

    Route::post('questions', [QuestionController::class, 'store']);
    Route::put('questions/{question}', [QuestionController::class, 'update']);
    Route::delete('questions/{question}', [QuestionController::class, 'destroy']);
    Route::post('questions/{question}/like', [QuestionController::class, 'toggleLike']);

    Route::post('questions/{question}/responses', [ResponseController::class, 'store']);
    Route::delete('responses/{response}', [ResponseController::class, 'destroy']);

    Route::post('events', [EventController::class, 'store']);
    Route::post('questions/{question}/events', [EventController::class, 'storeFromQuestion']);
    Route::post('events/{event}/join', [EventController::class, 'join']);
    Route::post('events/{event}/leave', [EventController::class, 'leave']);

    Route::get('conversations', [ConversationController::class, 'index']);
    Route::post('conversations', [ConversationController::class, 'store']);
    Route::get('conversations/{conversation}/messages', [ConversationController::class, 'messages']);
    Route::post('conversations/{conversation}/messages', [ConversationController::class, 'sendMessage']);

    Route::get('admin/stats', [AdminController::class, 'stats']);
    Route::get('admin/spots/pending', [AdminController::class, 'pendingSpots']);
    Route::post('admin/spots/{spot}/approve', [AdminController::class, 'approveSpot']);
    Route::post('admin/spots/{spot}/reject', [AdminController::class, 'rejectSpot']);
});

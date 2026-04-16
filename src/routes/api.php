<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\SportCategoryController;
use App\Http\Controllers\Api\SpotController;

//Public Routes

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public read access
Route::get('/sport-categories', [SportCategoryController::class, 'index']);
Route::get('/spots', [SpotController::class, 'index']);
Route::get('/spots/nearby', [SpotController::class, 'nearby']);
Route::get('/spots/{spot}', [SpotController::class, 'show']);
Route::get('/questions', [QuestionController::class, 'index']);
Route::get('/questions/{question}', [QuestionController::class, 'show']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);

//Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth & Profile
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Questions
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::put('/questions/{question}', [QuestionController::class, 'update']);
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy']);
    Route::post('/questions/{question}/like', [QuestionController::class, 'toggleLike']);

    // Responses
    Route::post('/questions/{question}/responses', [ResponseController::class, 'store']);
    Route::delete('/responses/{response}', [ResponseController::class, 'destroy']);

    // Spots
    Route::post('/spots', [SpotController::class, 'store']);
    Route::put('/spots/{spot}', [SpotController::class, 'update']);
    Route::delete('/spots/{spot}', [SpotController::class, 'destroy']);
    Route::post('/spots/{spot}/favorite', [SpotController::class, 'toggleFavorite']);
    Route::post('/spots/{spot}/reviews', [SpotController::class, 'addReview']);

    // Events
    Route::post('/events', [EventController::class, 'store']);
    Route::post('/questions/{question}/event', [EventController::class, 'storeFromQuestion']);
    Route::post('/events/{event}/join', [EventController::class, 'join']);
    Route::post('/events/{event}/leave', [EventController::class, 'leave']);

    // Conversations & Messages
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);
    Route::get('/conversations/{conversation}/messages', [ConversationController::class, 'messages']);
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage']);

    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'stats']);
        Route::get('/spots/pending', [AdminController::class, 'pendingSpots']);
        Route::put('/spots/{spot}/approve', [AdminController::class, 'approveSpot']);
        Route::put('/spots/{spot}/reject', [AdminController::class, 'rejectSpot']);
    });
});

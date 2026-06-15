<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LmsController;
use App\Http\Controllers\Api\IbadahTrackerController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SpiritualJournalController;
use App\Http\Controllers\Api\NoorAiController;

// Public Routes
Route::post('/login', [AuthController::class, 'login']); // Login Route
Route::get('/lms-levels', [LmsController::class, 'getLevelsWithModules']);

// Protected Routes (Requires Token)
Route::middleware(['auth:sanctum'])->group(function () {

    // Default user route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Ibadah Tracker Routes
    Route::post('/tracker', [IbadahTrackerController::class, 'saveDailyTracker']);
    Route::get('/tracker/history', [IbadahTrackerController::class, 'getHistory']); // <-- History Route

    // Spiritual Journal Routes
    Route::post('/journal', [SpiritualJournalController::class, 'saveJournal']);
    Route::get('/journal/history', [SpiritualJournalController::class, 'getHistory']); // <-- History Route

    // Noor AI Chat Routes
    Route::post('/noor-ai/chat', [NoorAiController::class, 'sendMessage']);
    Route::get('/noor-ai/history', [NoorAiController::class, 'getChatHistory']);
});
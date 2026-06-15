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

    // Get User Profile with Gamification Badges
    Route::get('/profile', function (Request $request) {
        $user = $request->user();
        $points = $user->total_points;

        // Gamification Logic (Badge Calculation)
        $badge = 'Seeker';
        if ($points >= 50)
            $badge = 'Fajr Warrior';
        if ($points >= 150)
            $badge = 'Consistent Believer';
        if ($points >= 300)
            $badge = 'Tazkiyah Master';

        return response()->json([
            'success' => true,
            'message' => 'User profile fetched successfully',
            'data' => [
                'user_info' => $user,
                'current_badge' => $badge,
                'current_points' => $points,
                'next_milestone' => ($points < 50) ? 50 : (($points < 150) ? 150 : (($points < 300) ? 300 : 'Max Level Reached'))
            ]
        ], 200);
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
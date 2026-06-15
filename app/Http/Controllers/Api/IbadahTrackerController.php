<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IbadahTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IbadahTrackerController extends Controller
{
    public function saveDailyTracker(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'date' => 'required|date',
            'fajr' => 'in:Jamaah,Individual,Missed',
            'dhuhr' => 'in:Jamaah,Individual,Missed',
            'asr' => 'in:Jamaah,Individual,Missed',
            'maghrib' => 'in:Jamaah,Individual,Missed',
            'isha' => 'in:Jamaah,Individual,Missed',
            'morning_adhkar' => 'boolean',
            'evening_adhkar' => 'boolean',
            'quran_recitation' => 'boolean',
        ]);

        // Find the currently authenticated user
        $user = Auth::user();

        // Use updateOrCreate to either update today's existing tracker or create a new one
        $tracker = IbadahTracker::updateOrCreate(
            // Condition to check: Does a tracker exist for this user on this specific date?
            ['user_id' => $user->id, 'date' => $validatedData['date']],

            // Data to update or insert
            $validatedData
        );
        // Give 10 points if the tracker is created for the very first time today
        if ($tracker->wasRecentlyCreated) {
            $user->increment('total_points', 10);
        }
        return response()->json([
            'success' => true,
            'message' => 'Daily Ibadah tracker saved successfully',
            'data' => $tracker
        ], 200);
    }
    public function getHistory()
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Fetch the user's tracker history, ordering by date (newest first)
        $history = IbadahTracker::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Ibadah tracker history fetched successfully',
            'data' => $history
        ], 200);
    }
}
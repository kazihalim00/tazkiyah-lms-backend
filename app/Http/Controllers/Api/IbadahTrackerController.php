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

        return response()->json([
            'success' => true,
            'message' => 'Daily Ibadah tracker saved successfully',
            'data' => $tracker
        ], 200);
    }
}
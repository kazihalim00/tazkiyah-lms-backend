<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IbadahTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IbadahTrackerController extends Controller
{
    public function index()
    {
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $tracker = IbadahTracker::where('user_id', auth()->id())->where('date', $today)->first();

        return view('tracker', compact('tracker'));
    }
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
    public function store(Request $request)
    {
        $user = auth()->user();
        $date = $request->input('date', \Carbon\Carbon::now()->format('Y-m-d'));

        // Find existing tracker or create a new instance
        $tracker = \App\Models\IbadahTracker::firstOrNew([
            'user_id' => $user->id,
            'date' => $date
        ]);

        // Assign request data to tracker fields
        $tracker->fajr = $request->fajr;
        $tracker->dhuhr = $request->dhuhr;
        $tracker->asr = $request->asr;
        $tracker->maghrib = $request->maghrib;
        $tracker->isha = $request->isha;
        $tracker->morning_adhkar = $request->has('morning_adhkar') ? 1 : 0;
        $tracker->evening_adhkar = $request->has('evening_adhkar') ? 1 : 0;
        $tracker->tahajjud = $request->has('tahajjud') ? 1 : 0;
        $tracker->witr = $request->has('witr') ? 1 : 0;
        $tracker->sadaqah = $request->has('sadaqah') ? 1 : 0;
        $tracker->duwa = $request->has('duwa') ? 1 : 0;
        $tracker->khushu_level = $request->input('khushu_level', 5);
        $tracker->quran_pages = $request->input('quran_pages', 0);
        $tracker->save();

        // Recalculate lifetime total points for the user to keep it strictly synced
        $allTrackers = \App\Models\IbadahTracker::where('user_id', $user->id)->get();
        $totalPoints = 0;

        foreach ($allTrackers as $t) {
            // 1. Obligatory Prayers Points
            $prayers = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];
            foreach ($prayers as $prayer) {
                if ($t->$prayer === 'jamaah_mosque')
                    $totalPoints += 10;
                elseif ($t->$prayer === 'jamaah_home')
                    $totalPoints += 7;
                elseif ($t->$prayer === 'alone')
                    $totalPoints += 5;
                elseif ($t->$prayer === 'qada')
                    $totalPoints += 2;
            }

            // 2. Sunnah & Good Deeds Points
            $deeds = ['morning_adhkar', 'evening_adhkar', 'tahajjud', 'witr', 'sadaqah', 'duwa'];
            foreach ($deeds as $deed) {
                if ($t->$deed == 1)
                    $totalPoints += 5;
            }

            // 3. Quran Recitation Points (2 points per page)
            if ($t->quran_pages > 0) {
                $totalPoints += ($t->quran_pages * 2);
            }

            // 4. Khushu Level Points
            if ($t->khushu_level > 0) {
                $totalPoints += $t->khushu_level;
            }
        }

        // Update user's cumulative total points in the database
        $user->total_points = $totalPoints;
        $user->save();

        return back()->with('success', "Today's spiritual progress saved successfully!");
    }
}
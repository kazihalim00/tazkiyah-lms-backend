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
        $data = $request->except(['_token']);
        $data['user_id'] = $user->id;
        $data['date'] = \Carbon\Carbon::now()->format('Y-m-d');

        // 1. Calculate points for today
        $newPoints = 0;
        $prayers = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];

        foreach ($prayers as $p) {
            if ($user->gender == 'male') {
                if ($request->$p == 'jamaah_mosque')
                    $newPoints += 10;
                elseif (in_array($request->$p, ['jamaah_home', 'alone']))
                    $newPoints += 5;
            } else {
                // Female full points for timely prayer
                if (in_array($request->$p, ['jamaah_home', 'alone', 'jamaah_mosque']))
                    $newPoints += 10;
            }
        }

        if ($request->has('morning_adhkar'))
            $newPoints += 2;
        if ($request->has('sadaqah'))
            $newPoints += 5;

        // 2. Check if an entry already exists for today to avoid duplicate points
        $existingTracker = IbadahTracker::where('user_id', $user->id)
            ->where('date', $data['date'])
            ->first();

        if ($existingTracker) {
            // Option A: If you want to replace points, subtract old and add new
            // $user->total_points = ($user->total_points - $existingTracker->points_earned_today) + $newPoints;

            // OR Option B (Simpler): Just update the tracker, and add only the difference if needed.
            // For now, let's keep it simple: we update the tracker.
        }

        // 3. Save the tracker entry
        // It's better to store the points earned today in the tracker table itself
        $data['points_earned_today'] = $newPoints;

        IbadahTracker::updateOrCreate(
            ['user_id' => $user->id, 'date' => $data['date']],
            $data
        );

        // 4. Update User total points (Safe way)
        // Here we update the total points. Ideally, use an Observer or calculate on the fly.
        $user->total_points = IbadahTracker::where('user_id', $user->id)->sum('points_earned_today');
        $user->save();

        return back()->with('success', 'Alhamdulillah! Your progress saved. You earned ' . $newPoints . ' points for today.');
    }
}
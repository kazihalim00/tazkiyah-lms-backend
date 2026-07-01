<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IbadahTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IbadahTrackerController extends Controller
{
    public function index()
    {
        // Force Bangladesh Timezone (Asia/Dhaka)
        $today = Carbon::now('Asia/Dhaka')->format('Y-m-d');

        // Fetch today's tracker so the form can load previously saved data
        $tracker = IbadahTracker::where('user_id', auth()->id())->where('date', $today)->first();

        return view('tracker', compact('tracker'));
    }

    public function saveDailyTracker(Request $request)
    {
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

        $user = Auth::user();

        $tracker = IbadahTracker::updateOrCreate(
            ['user_id' => $user->id, 'date' => $validatedData['date']],
            $validatedData
        );

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
        $user = Auth::user();

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

        // Force Bangladesh Timezone
        $date = $request->input('date', Carbon::now('Asia/Dhaka')->format('Y-m-d'));

        // firstOrNew ensures only ONE record exists per day.
        $tracker = IbadahTracker::firstOrNew([
            'user_id' => $user->id,
            'date' => $date
        ]);

        // 1. Calculate the score BEFORE applying new updates
        $oldScore = $this->calculateDailyScore($tracker);

        // 🟢 ANTI-CHEAT LOGIC: Prevent downgrades!
        // We assign numerical ranks to prayer types to ensure users can only UPGRADE their status.
        $prayerRanks = [
            'missed' => 0,
            'qada' => 1,
            'alone' => 2,
            'individual' => 2,
            'jamaah_home' => 3,
            'jamaah' => 4,
            'jamaah_mosque' => 4
        ];

        $prayers = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];
        foreach ($prayers as $prayer) {
            $newVal = strtolower($request->$prayer ?? 'missed');
            $oldVal = strtolower($tracker->$prayer ?? 'missed');

            $newRank = $prayerRanks[$newVal] ?? 0;
            $oldRank = $prayerRanks[$oldVal] ?? 0;

            // Only update the database if the new input is a higher or equal rank
            if ($newRank > $oldRank) {
                $tracker->$prayer = $request->$prayer;
            }
        }

        // 🟢 ANTI-CHEAT LOGIC for Booleans: Once checked (1), it CANNOT be unchecked (0)
        // This prevents users from toggling checkboxes to farm points infinitely
        $tracker->morning_adhkar = $tracker->morning_adhkar || $request->has('morning_adhkar') ? 1 : 0;
        $tracker->evening_adhkar = $tracker->evening_adhkar || $request->has('evening_adhkar') ? 1 : 0;
        $tracker->tahajjud = $tracker->tahajjud || $request->has('tahajjud') ? 1 : 0;
        $tracker->witr = $tracker->witr || $request->has('witr') ? 1 : 0;
        $tracker->sadaqah = $tracker->sadaqah || $request->has('sadaqah') ? 1 : 0;
        $tracker->duwa = $tracker->duwa || $request->has('duwa') ? 1 : 0;

        // 🟢 ANTI-CHEAT LOGIC for Numerics: Keep the highest value recorded today
        $tracker->khushu_level = max($tracker->khushu_level ?? 0, (int) $request->input('khushu_level', 5));
        $tracker->quran_pages = max($tracker->quran_pages ?? 0, (int) $request->input('quran_pages', 0));

        // Save the fortified tracker data
        $tracker->save();

        // 2. Calculate the score AFTER applying updates
        $newScore = $this->calculateDailyScore($tracker);

        // 3. Find the exact point difference earned from this submission
        $pointsEarnedToday = $newScore - $oldScore;

        // 4. Safely ADD points without overwriting points from other sources (like Quizzes)
        if ($pointsEarnedToday > 0) {
            $user->increment('total_points', $pointsEarnedToday);
        }

        return redirect('/my-dashboard')->with('success', "Alhamdulillah! Today's spiritual progress saved successfully. Keep watering your tree! 💧");
    }

    /**
     * Helper method to calculate points for a SINGLE daily tracker row.
     */
    private function calculateDailyScore($tracker)
    {
        if (!$tracker)
            return 0;

        $score = 0;
        $prayers = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];

        foreach ($prayers as $prayer) {
            $status = strtolower($tracker->$prayer ?? '');
            if (in_array($status, ['jamaah', 'jamaah_mosque'])) {
                $score += 10;
            } elseif ($status === 'jamaah_home') {
                $score += 7;
            } elseif (in_array($status, ['individual', 'alone'])) {
                $score += 5;
            } elseif ($status === 'qada') {
                $score += 2;
            }
        }

        $deeds = ['morning_adhkar', 'evening_adhkar', 'tahajjud', 'witr', 'sadaqah', 'duwa'];
        foreach ($deeds as $deed) {
            if ($tracker->$deed == 1) {
                $score += 5;
            }
        }

        if ($tracker->quran_pages > 0) {
            $score += ($tracker->quran_pages * 2);
        }

        if ($tracker->khushu_level > 0) {
            $score += $tracker->khushu_level;
        }

        return $score;
    }
}
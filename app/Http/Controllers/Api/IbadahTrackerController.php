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
        // 🟢 FIXED: Force Bangladesh Timezone (Asia/Dhaka)
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

        // 🟢 FIXED: Force Bangladesh Timezone (Asia/Dhaka)
        $date = $request->input('date', Carbon::now('Asia/Dhaka')->format('Y-m-d'));

        // firstOrNew ensures only ONE record exists per day. 
        // If a user submits multiple times, it just updates today's record.
        $tracker = IbadahTracker::firstOrNew([
            'user_id' => $user->id,
            'date' => $date
        ]);

        // Assign request data
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

        // Recalculate lifetime total points dynamically
        $allTrackers = IbadahTracker::where('user_id', $user->id)->get();
        $totalPoints = 0;

        foreach ($allTrackers as $t) {
            $prayers = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];
            foreach ($prayers as $prayer) {
                $status = strtolower($t->$prayer);
                if (in_array($status, ['jamaah', 'jamaah_mosque'])) {
                    $totalPoints += 10;
                } elseif ($status === 'jamaah_home') {
                    $totalPoints += 7;
                } elseif (in_array($status, ['individual', 'alone'])) {
                    $totalPoints += 5;
                } elseif ($status === 'qada') {
                    $totalPoints += 2;
                }
            }

            $deeds = ['morning_adhkar', 'evening_adhkar', 'tahajjud', 'witr', 'sadaqah', 'duwa'];
            foreach ($deeds as $deed) {
                if ($t->$deed == 1) {
                    $totalPoints += 5;
                }
            }

            if ($t->quran_pages > 0) {
                $totalPoints += ($t->quran_pages * 2);
            }
            if ($t->khushu_level > 0) {
                $totalPoints += $t->khushu_level;
            }
        }

        $user->total_points = $totalPoints;
        $user->save();

        return redirect('/my-dashboard')->with('success', "Alhamdulillah! Today's spiritual progress saved successfully. Keep watering your tree! 💧");
    }
}
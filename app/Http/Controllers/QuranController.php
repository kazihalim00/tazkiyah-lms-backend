<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Surah;
use App\Models\QuranProgress;
use App\Models\IbadahTracker; // Added for tracker
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuranController extends Controller
{
    public function index()
    {
        $surahs = Surah::orderBy('surah_no', 'asc')->get();
        return view('quran.index', compact('surahs'));
    }

    public function show($id)
    {
        // Load Surah with its Ayahs. Make sure 'ayahs' relation is defined in Surah model.
        $surah = Surah::with('ayahs')->findOrFail($id);

        // Fetch the user's last read Ayah ID from session or database
        $user = Auth::user();
        $lastReadAyahId = $user->last_read_ayah_id ?? null;

        return view('quran.show', compact('surah', 'lastReadAyahId'));
    }

    public function saveTadabbur(Request $request, $ayahId)
    {
        $request->validate([
            'tadabbur_note' => 'required|string|min:10',
            'reference' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            $progress = QuranProgress::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'ayah_id' => $ayahId
                ],
                [
                    'tadabbur_note' => $request->tadabbur_note,
                    'reference' => $request->reference,
                    'is_read' => true,
                    'points_earned' => 5
                ]
            );

            $user = Auth::user();
            $user->increment('total_points', 5); // Updated column name to total_points

            DB::commit();
            return redirect()->back()->with('success', 'Alhamdulillah! Tadabbur saved (5 points).');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    // 🟢 UPDATED: Now returns JSON response for AJAX (No Page Reload)
    public function markAyahAsRead($id)
    {
        $user = auth()->user();
        $sessionKey = 'read_ayah_' . $id . '_' . $user->id;

        // Prevent multiple claims in the same session
        if (session()->has($sessionKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Already claimed points for this Ayah.'
            ], 400);
        }

        // Increment points
        $user->increment('total_points', 5);
        session()->put($sessionKey, true);

        // Update Daily Tracker Bonus Points
        $tracker = IbadahTracker::where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        if (!$tracker) {
            $tracker = new IbadahTracker();
            $tracker->user_id = $user->id;
            $tracker->date = now()->toDateString();
            $tracker->save();
        }
        $tracker->increment('bonus_points', 5);

        // Return JSON success response
        return response()->json([
            'success' => true,
            'message' => '5 points earned!',
            'new_total' => $user->total_points
        ]);
    }

    // 🟢 NEW: Saves the last read position in the background
    public function saveLastRead(Request $request)
    {
        $user = Auth::user();

        // Ensure you have a 'last_read_ayah_id' column in your 'users' table
        // If not, create a migration: $table->unsignedBigInteger('last_read_ayah_id')->nullable();
        $user->last_read_ayah_id = $request->ayah_id;
        $user->save();

        return response()->json(['success' => true]);
    }
}
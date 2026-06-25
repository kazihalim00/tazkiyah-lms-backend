<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Surah;
use App\Models\QuranProgress; // Imported for Tadabbur
use Illuminate\Support\Facades\DB; // Imported for Transactions
use Illuminate\Support\Facades\Auth; // Imported for Auth

class QuranController extends Controller
{
    public function index()
    {
        $surahs = Surah::orderBy('surah_no', 'asc')->get();
        return view('quran.index', compact('surahs'));
    }

    public function show($id)
    {
        // 'with('ayahs')' will load all ayahs including their tafsir if present in DB
        $surah = Surah::with('ayahs')->findOrFail($id);
        return view('quran.show', compact('surah'));
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

            // Updating user points
            $user = Auth::user();
            $user->increment('points', 5);

            DB::commit();
            return redirect()->back()->with('success', 'Alhamdulillah! Your Tadabbur has been saved, and you earned 5 points.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Sorry, something went wrong. Please try again.');
        }
    }
}
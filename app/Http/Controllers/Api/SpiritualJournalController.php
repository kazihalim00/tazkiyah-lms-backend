<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SpiritualJournal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpiritualJournalController extends Controller
{
    public function saveJournal(Request $request)
    {
        // Validate the incoming text and date
        $request->validate([
            'date' => 'required|date',
            'entry_text' => 'required|string',
        ]);

        $user = Auth::user();

        // Use updateOrCreate so the user can edit today's journal multiple times
        $journal = SpiritualJournal::updateOrCreate(
            ['user_id' => $user->id, 'date' => $request->date],
            ['entry_text' => $request->entry_text]
        );

        return response()->json([
            'success' => true,
            'message' => 'Spiritual journal entry saved successfully',
            'data' => $journal
        ], 200);
    }
    public function getHistory()
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Fetch the user's journal history, ordering by date (newest first)
        $history = SpiritualJournal::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Spiritual journal history fetched successfully',
            'data' => $history
        ], 200);
    }
}
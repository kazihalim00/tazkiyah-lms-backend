<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AccountabilityPartner;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Global Top 10 users regardless of friendship status
        $leaderboard = User::orderBy('total_points', 'desc')->take(10)->get();

        // Get connected or requested user IDs to manage buttons on leaderboard
        $existingConnections = AccountabilityPartner::where('user_id', $user->id)
            ->orWhere('partner_id', $user->id)
            ->get();

        $connectedUserIds = [];
        $pendingSentIds = [];
        $pendingReceivedIds = [];

        foreach ($existingConnections as $conn) {
            $targetId = ($conn->user_id == $user->id) ? $conn->partner_id : $conn->user_id;
            if ($conn->status === 'accepted') {
                $connectedUserIds[] = $targetId;
            } elseif ($conn->status === 'pending') {
                if ($conn->user_id == $user->id) {
                    $pendingSentIds[] = $targetId;
                } else {
                    $pendingReceivedIds[] = $targetId;
                }
            }
        }

        return view('leaderboard.index', compact('leaderboard', 'connectedUserIds', 'pendingSentIds', 'pendingReceivedIds'));
    }
}
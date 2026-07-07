<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    public function index()
    {
        $authId = Auth::id();
        // 🟢 Get current user's gender
        $userGender = Auth::user()->gender ?? 'male';

        // 🟢 Fetch top 50 users strictly of the SAME gender
        $leaderboard = User::where('is_admin', 0)
            ->where('gender', $userGender)
            ->orderBy('total_points', 'desc')
            ->take(50)
            ->get();

        $connectedUserIds = [];
        $pendingSentIds = [];

        $connections = DB::table('accountability_partners')
            ->where('user_id', $authId)
            ->orWhere('partner_id', $authId)
            ->get();

        foreach ($connections as $connection) {
            $status = strtolower($connection->status ?? '');

            if ($status === 'accepted' || $status === 'approved') {
                $connectedUserIds[] = ($connection->user_id == $authId) ? $connection->partner_id : $connection->user_id;
            } elseif ($status === 'pending' && $connection->user_id == $authId) {
                $pendingSentIds[] = $connection->partner_id;
            }
        }

        return view('leaderboard.index', compact('leaderboard', 'connectedUserIds', 'pendingSentIds'));
    }
}
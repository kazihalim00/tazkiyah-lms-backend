<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index()
    {
        // ১. শীর্ষ ৫০ জন সাধারণ ইউজারকে নিয়ে আসা (অ্যাডমিন বাদে)
        $leaderboard = User::where('is_admin', 0)
            ->orderBy('total_points', 'desc')
            ->take(50)
            ->get();

        $authId = auth()->id();
        $connectedUserIds = [];
        $pendingSentIds = [];

        // ২. তোমার আসল টেবিল ও কলাম (user_id, partner_id) অনুযায়ী কুয়েরি
        $connections = DB::table('accountability_partners')
            ->where('user_id', $authId)
            ->orWhere('partner_id', $authId)
            ->get();

        foreach ($connections as $connection) {
            $status = strtolower($connection->status ?? '');

            if ($status === 'accepted' || $status === 'approved') {
                // কানেক্টেড পার্টনারের আইডি আলাদা করা
                $connectedUserIds[] = ($connection->user_id == $authId) ? $connection->partner_id : $connection->user_id;
            } elseif ($status === 'pending' && $connection->user_id == $authId) {
                // আমি যাদের রিকোয়েস্ট পাঠিয়েছি এবং এখনো পেন্ডিং আছে
                $pendingSentIds[] = $connection->partner_id;
            }
        }

        // ৩. সব ডাটা একসাথে ব্লেড ভিউতে পাস করা
        return view('leaderboard.index', compact('leaderboard', 'connectedUserIds', 'pendingSentIds'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AccountabilityPartner;
use App\Models\IbadahTracker;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AccountabilityPartnerController extends Controller
{
    /**
     * Display leaderboard, pending requests, active partners, and suggestions.
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::now()->format('Y-m-d');

        // Fallback to 'male' if user gender is not set to prevent query crashes
        $userGender = $user->gender ?? 'male';

        // 1. Fetch Top 10 users for Leaderboard based on gender
        $leaderboard = User::where('gender', $userGender)
            ->get()
            ->sortByDesc(function ($u) {
                return (int) ($u->total_points ?? 0);
            })
            ->take(10)
            ->values();

        // 2. Fetch pending requests received by the authenticated user
        $pendingRequests = AccountabilityPartner::where('partner_id', $user->id)
            ->where('status', '=', 'pending')
            ->with('user')
            ->get();

        // 3. Get IDs of all connections to filter out suggestions
        $existingConnections = AccountabilityPartner::where('user_id', $user->id)
            ->orWhere('partner_id', $user->id)
            ->get();

        $connectedUserIds = $existingConnections->pluck('user_id')
            ->merge($existingConnections->pluck('partner_id'))
            ->unique()
            ->toArray();

        // 4. Fetch Active Partners (Accepted connections) and include their today's progress
        $acceptedConnections = AccountabilityPartner::where('status', 'accepted')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('partner_id', $user->id);
            })->get();

        $activePartnerIds = $acceptedConnections->map(function ($conn) use ($user) {
            return $conn->user_id == $user->id ? $conn->partner_id : $conn->user_id;
        });

        $activePartners = User::whereIn('id', $activePartnerIds)->get()->map(function ($partner) use ($today) {
            $partner->today_tracker = IbadahTracker::where('user_id', $partner->id)
                ->whereDate('date', $today)
                ->first();
            return $partner;
        });

        // 5. Fetch suggested partners matching same gender, excluding self and connections
        $suggestedPartners = User::where('gender', $userGender)
            ->where('id', '!=', $user->id)
            ->whereNotIn('id', $connectedUserIds)
            ->get()
            ->sortByDesc(function ($u) {
                return (int) ($u->total_points ?? 0);
            })
            ->take(6)
            ->values();

        return view('partner.index', compact('leaderboard', 'pendingRequests', 'activePartners', 'suggestedPartners'));
    }

    /**
     * Send a partner request to another user.
     */
    public function sendRequest(Request $request, $partnerId)
    {
        $sender = Auth::user();
        $receiver = User::findOrFail($partnerId);

        // Strict Security Check
        if ($sender->gender !== $receiver->gender) {
            return back()->with('error', 'Cross-gender connection requests are strictly prohibited.');
        }

        $exists = AccountabilityPartner::where(function ($query) use ($partnerId) {
            $query->where('user_id', Auth::id())->where('partner_id', $partnerId);
        })->orWhere(function ($query) use ($partnerId) {
            $query->where('user_id', $partnerId)->where('partner_id', Auth::id());
        })->exists();

        if ($exists) {
            return back()->with('error', 'A connection or request already exists.');
        }

        AccountabilityPartner::create([
            'user_id' => Auth::id(),
            'partner_id' => $partnerId,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Partner request sent successfully.');
    }

    /**
     * Accept an incoming partner request.
     */
    public function acceptRequest($id)
    {
        $request = AccountabilityPartner::findOrFail($id);

        if ($request->partner_id == Auth::id()) {
            $request->update(['status' => 'accepted']);
            return back()->with('success', 'Partner request accepted successfully.');
        }

        return back()->with('error', 'Unauthorized action.');
    }

    /**
     * Reject or cancel a partner request.
     */
    public function rejectRequest($id)
    {
        $request = AccountabilityPartner::findOrFail($id);

        if ($request->user_id == Auth::id() || $request->partner_id == Auth::id()) {
            $request->delete();
            return back()->with('success', 'Request or partnership removed.');
        }

        return back()->with('error', 'Unauthorized action.');
    }
}
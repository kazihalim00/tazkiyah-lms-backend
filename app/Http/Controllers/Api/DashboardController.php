<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IbadahTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Required for date calculations

class DashboardController extends Controller
{
    public function getDashboardSummary(Request $request)
    {
        $user = Auth::user();
        $points = $user->total_points;

        // Fetch badge, icon, and tree stage directly from User model
        $badgeData = $user->badge;

        // Calculate the date 7 days ago
        $sevenDaysAgo = Carbon::now()->subDays(7)->format('Y-m-d');

        // Fetch user's tracker data for the last 7 days
        $weeklyTrackers = IbadahTracker::where('user_id', $user->id)
            ->where('date', '>=', $sevenDaysAgo)
            ->orderBy('date', 'asc')
            ->get();

        // Analytics: Calculate total Jamaah vs Missed for Fajr
        $fajrJamaah = $weeklyTrackers->where('fajr', 'Jamaah')->count();
        $fajrMissed = $weeklyTrackers->where('fajr', 'Missed')->count();

        // Total dhikr completion in the last 7 days
        $morningAdhkarCount = $weeklyTrackers->where('morning_adhkar', true)->count();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard analytics fetched successfully',
            'data' => [
                'user_summary' => [
                    'name' => $user->name,
                    'current_points' => $points,
                    'current_badge' => $badgeData['name'],
                    'badge_icon' => $badgeData['icon'],
                    'tree_stage' => $badgeData['tree_stage'], // Added tree stage indicator (1 to 8)
                ],
                'weekly_analytics' => [
                    'days_tracked_this_week' => $weeklyTrackers->count(),
                    'fajr_jamaah_count' => $fajrJamaah,
                    'fajr_missed_count' => $fajrMissed,
                    'morning_adhkar_count' => $morningAdhkarCount,
                ],
                'recent_entries' => $weeklyTrackers
            ]
        ], 200);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Models\HadithCategory;
use App\Models\HadithSubCategory;
use App\Models\UserHadithProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HadithController extends Controller
{
    public function index()
    {
        $categories = HadithCategory::withCount('hadiths')->get();
        return view('hadiths.index', compact('categories'));
    }

    public function category($slug)
    {
        $category = HadithCategory::where('slug', $slug)->firstOrFail();
        $subCategories = HadithSubCategory::where('category_id', $category->id)->withCount('hadiths')->get();
        $uncategorizedHadiths = Hadith::where('category_id', $category->id)
            ->whereNull('sub_category_id')
            ->orderBy('hadith_number', 'asc')
            ->paginate(20);

        return view('hadiths.category', compact('category', 'subCategories', 'uncategorizedHadiths'));
    }

    public function chapter($id)
    {
        $subCategory = HadithSubCategory::with('category')->findOrFail($id);
        $hadiths = Hadith::where('sub_category_id', $id)->orderBy('hadith_number', 'asc')->get();

        return view('hadiths.chapter', compact('subCategory', 'hadiths'));
    }

    // 🟢 UPDATED: AJAX Method for claiming Hadith points without reload
    public function markAsRead($id)
    {
        $hadith = Hadith::findOrFail($id);
        $user = Auth::user();
        $pointsToGive = $hadith->points ?? 5; // Default to 5 if empty

        // Check if user has already read this Hadith
        if ($hadith->isReadBy($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Already claimed points for this Hadith.'
            ], 400);
        }

        // Save progress to database
        UserHadithProgress::create([
            'user_id' => $user->id,
            'hadith_id' => $hadith->id
        ]);

        // Add points to user's total points
        $user->increment('total_points', $pointsToGive);

        // Return JSON success response
        return response()->json([
            'success' => true,
            'message' => $pointsToGive . ' points earned!',
            'new_total' => $user->total_points
        ]);
    }
}
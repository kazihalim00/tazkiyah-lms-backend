<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Models\HadithCategory;
use App\Models\HadithSubCategory;
use App\Models\UserHadithProgress;
use Illuminate\Http\Request;

class HadithController extends Controller
{
    public function index()
    {
        $categories = \App\Models\HadithCategory::withCount('hadiths')->get();
        return view('hadiths.index', compact('categories'));
    }

    // Missing Category Method Added
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

    // Missing Chapter Method Added
    public function chapter($id)
    {
        $subCategory = HadithSubCategory::with('category')->findOrFail($id);
        $hadiths = Hadith::where('sub_category_id', $id)->orderBy('hadith_number', 'asc')->get();

        return view('hadiths.chapter', compact('subCategory', 'hadiths'));
    }

    public function markAsRead($id)
    {
        $hadith = Hadith::findOrFail($id);
        $user = auth()->user();

        if (!$hadith->isReadBy($user->id)) {
            UserHadithProgress::create(['user_id' => $user->id, 'hadith_id' => $hadith->id]);
            $user->increment('total_points', $hadith->points);
            return back()->with('success', 'Alhamdulillah! You earned ' . $hadith->points . ' points.');
        }
        return back()->with('info', 'Already read.');
    }
}
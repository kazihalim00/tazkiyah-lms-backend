<?php

namespace App\Http\Controllers;

use App\Models\HadithCategory;
use App\Models\Hadith;
use App\Models\UserHadithProgress;
use Illuminate\Http\Request;

class HadithController extends Controller
{
    public function index()
    {
        $categories = HadithCategory::withCount('hadiths')->get();
        return view('hadiths.index', compact('categories'));
    }

    public function category($slug)
    {
        $category = \App\Models\HadithCategory::where('slug', $slug)->firstOrFail();

        // Ekhane amra sudhu poricched (sub-category) gulor list anbo count soho
        $subCategories = \App\Models\HadithSubCategory::where('category_id', $category->id)
            ->withCount('hadiths')
            ->get();

        // Jodi emon kono hadith thake jar kono poricched nai
        $uncategorizedHadiths = \App\Models\Hadith::where('category_id', $category->id)
            ->whereNull('sub_category_id')
            ->latest()->get();

        return view('hadiths.show', compact('category', 'subCategories', 'uncategorizedHadiths'));
    }

    public function chapter($id)
    {

        $subCategory = \App\Models\HadithSubCategory::with('category')->findOrFail($id);
        $hadiths = \App\Models\Hadith::where('sub_category_id', $id)->latest()->get();

        return view('hadiths.chapter', compact('subCategory', 'hadiths'));
    }

    public function markAsRead($id)
    {
        $hadith = \App\Models\Hadith::findOrFail($id);
        $user = auth()->user();


        if (!$hadith->isReadBy($user->id)) {
            \App\Models\UserHadithProgress::create([
                'user_id' => $user->id,
                'hadith_id' => $hadith->id
            ]);


            $user->increment('total_points', $hadith->points);

            return back()->with('success', 'Alhamdulillah! ' . $hadith->points . ' points added to your profile for gaining knowledge.');
        }

        return back()->with('info', 'You have already read this hadith.');
    }

}
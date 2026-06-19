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
        $category = HadithCategory::where('slug', $slug)->firstOrFail();
        $hadiths = $category->hadiths()->latest()->get();
        return view('hadiths.show', compact('category', 'hadiths'));
    }

    public function markAsRead($id)
    {
        $hadith = Hadith::findOrFail($id);
        $user = auth()->user();

        if (!$hadith->isReadBy($user->id)) {
            UserHadithProgress::create([
                'user_id' => $user->id,
                'hadith_id' => $hadith->id
            ]);

            if (\Schema::hasColumn('users', 'points')) {
                $user->increment('points', $hadith->points);
            }

            return back()->with('success', 'Alhamdulillah! ' . $hadith->points . ' points added to your profile for gaining knowledge.');
        }

        return back()->with('info', 'You have already read this hadith.');
    }
}
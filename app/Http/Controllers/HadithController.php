<?php

namespace App\Http\Controllers;

use App\Models\HadithCategory;
use App\Models\Hadith;
use App\Models\HadithSubCategory;
use App\Models\UserHadithProgress;
use Illuminate\Http\Request;

class HadithController extends Controller
{
    public function index()
    {
        $hadiths = \App\Models\Hadith::with('category')->paginate(20);
        return view('hadiths.index', compact('hadiths'));
    }

    public function category($slug)
    {
        $category = HadithCategory::where('slug', $slug)->firstOrFail();

        $subCategories = HadithSubCategory::where('category_id', $category->id)
            ->withCount('hadiths')
            ->get();

        // সাব-ক্যাটাগরি ছাড়া যে হাদিসগুলো এই ক্যাটাগরিতে আছে, সেগুলোতে Paginate যোগ করা হলো (প্রতি পেজে ২০টি)
        $uncategorizedHadiths = Hadith::where('category_id', $category->id)
            ->whereNull('sub_category_id')
            ->orderBy('hadith_number', 'asc')
            ->paginate(20);

        return view('hadiths.category', compact('category', 'subCategories', 'uncategorizedHadiths'));
    }

    public function chapter($id)
    {
        $subCategory = \App\Models\HadithSubCategory::with('category')->findOrFail($id);


        $hadiths = \App\Models\Hadith::where('sub_category_id', $id)
            ->orderBy('id', 'asc')->get();

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
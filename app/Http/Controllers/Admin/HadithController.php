<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hadith;
use App\Models\HadithCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HadithController extends Controller
{
    public function index()
    {
        $hadiths = Hadith::with('category')->latest()->get();
        return view('admin.hadiths.index', compact('hadiths'));
    }

    public function create()
    {
        $categories = HadithCategory::all();
        return view('admin.hadiths.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'arabic_text' => 'required',
            'bangla_text' => 'required',
            'reference' => 'required',
        ]);

        $categoryId = $request->category_id;

        if ($request->filled('new_category_bn')) {
            $category = HadithCategory::create([
                'name_bn' => $request->new_category_bn,
                'name_en' => $request->new_category_en,
                'slug' => Str::slug($request->new_category_en ?? $request->new_category_bn) . '-' . time()
            ]);
            $categoryId = $category->id;
        }

        Hadith::create([
            'category_id' => $categoryId,
            'arabic_text' => $request->arabic_text,
            'bangla_text' => $request->bangla_text,
            'english_text' => $request->english_text,
            'reference' => $request->reference,
            'grade' => $request->grade ?? 'সহীহ',
            'explanation' => $request->explanation,
            'source_url' => $request->source_url,
            'points' => $request->points ?? 5,
        ]);

        return redirect()->back()->with('success', 'Hadith added successfully!');
    }
}
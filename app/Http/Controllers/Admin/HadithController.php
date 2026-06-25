<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hadith;
use App\Models\HadithCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HadithController extends Controller
{
    public function index(Request $request)
    {
        $query = Hadith::with('category')->orderBy('hadith_number', 'asc');

 
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('bangla_text', 'like', '%' . $search . '%')
                ->orWhere('arabic_text', 'like', '%' . $search . '%')
                ->orWhere('grade', 'like', '%' . $search . '%')
                ->orWhere('hadith_number', 'like', '%' . $search . '%');
        }


        $hadiths = $query->paginate(50);

        return view('admin.hadiths.index', compact('hadiths'));
    }

    public function create()
    {
        $categories = HadithCategory::with('subCategories')->get();
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

        $subCategoryId = $request->sub_category_id;

        if ($request->filled('new_sub_category_bn') && $categoryId) {
            $subCategory = \App\Models\HadithSubCategory::create([
                'category_id' => $categoryId,
                'name_bn' => $request->new_sub_category_bn,
                'name_en' => $request->new_sub_category_en,
            ]);
            $subCategoryId = $subCategory->id;
        }

        Hadith::create([
            'category_id' => $categoryId,
            'sub_category_id' => $subCategoryId,
            'hadith_number' => $request->hadith_number, // হাদিস নাম্বার যুক্ত করা হলো
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

    public function edit($id)
    {
        $hadith = Hadith::findOrFail($id);
        $categories = HadithCategory::with('subCategories')->get();
        return view('admin.hadiths.edit', compact('hadith', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'arabic_text' => 'required',
            'bangla_text' => 'required',
            'reference' => 'required',
        ]);

        $hadith = Hadith::findOrFail($id);
        $hadith->update([
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'hadith_number' => $request->hadith_number,
            'arabic_text' => $request->arabic_text,
            'bangla_text' => $request->bangla_text,
            'english_text' => $request->english_text,
            'reference' => $request->reference,
            'grade' => $request->grade ?? 'সহীহ',
            'explanation' => $request->explanation,
            'source_url' => $request->source_url,
            'points' => $request->points ?? 5,
        ]);

        return redirect()->route('admin.hadiths.index')->with('success', 'Hadith updated successfully!');
    }
}
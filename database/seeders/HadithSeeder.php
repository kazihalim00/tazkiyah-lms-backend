<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HadithCategory;
use App\Models\HadithSubCategory;
use App\Models\Hadith;
use Illuminate\Support\Str;

class HadithSeeder extends Seeder
{
    public function run()
    {
        // ১. একটি নতুন ক্যাটাগরি তৈরি
        $category = HadithCategory::create([
            'name_bn' => 'ওহীর সূচনা',
            'name_en' => 'Revelation',
            'slug' => Str::slug('Revelation') . '-' . time()
        ]);

        // ২. এই ক্যাটাগরির আন্ডারে ২টি সাব-ক্যাটাগরি (পরিচ্ছেদ) তৈরি
        $subCategory1 = HadithSubCategory::create([
            'category_id' => $category->id,
            'name_bn' => 'ওহী কীভাবে নাযিল হতো',
            'name_en' => 'How Revelation Started',
        ]);

        $subCategory2 = HadithSubCategory::create([
            'category_id' => $category->id,
            'name_bn' => 'হেরা গুহায় প্রথম ওহী',
            'name_en' => 'First Revelation in Cave Hira',
        ]);

        // ৩. সাব-ক্যাটাগরি ১ এর আন্ডারে হাদিস যুক্ত করা
        Hadith::create([
            'category_id' => $category->id,
            'sub_category_id' => $subCategory1->id,
            'hadith_number' => '১',
            'arabic_text' => 'إِنَّمَا الأَعْمَالُ بِالنِّيَّاتِ...',
            'bangla_text' => 'উমর ইবনুল খাত্তাব (রাঃ) থেকে বর্ণিত। রাসূলুল্লাহ (ﷺ) বলেছেন: "যাবতীয় কাজ নিয়তের উপর নির্ভরশীল..."',
            'english_text' => 'The reward of deeds depends upon the intentions...',
            'reference' => 'সহীহ বুখারী, হাদিস ১',
            'grade' => 'সহীহ',
            'points' => 5,
        ]);

        // ৪. সাব-ক্যাটাগরি ২ এর আন্ডারে হাদিস যুক্ত করা
        Hadith::create([
            'category_id' => $category->id,
            'sub_category_id' => $subCategory2->id,
            'hadith_number' => '৩',
            'arabic_text' => 'عَائِشَةَ أُمِّ الْمُؤْمِنِينَ، أَنَّهَا قَالَتْ...',
            'bangla_text' => 'উম্মুল মুমিনীন আয়িশা (রাঃ) থেকে বর্ণিত। তিনি বলেন, আল্লাহর রাসূল (ﷺ)-এর নিকট সর্বপ্রথম যে ওহী আসে, তা ছিল নিদ্রাবস্থায় সত্য স্বপ্নরূপে...',
            'english_text' => 'Aisha, the mother of the believers, narrated...',
            'reference' => 'সহীহ বুখারী, হাদিস ৩',
            'grade' => 'সহীহ',
            'points' => 10,
        ]);

        // ৫. কোনো সাব-ক্যাটাগরি ছাড়া সরাসরি ক্যাটাগরির আন্ডারে একটি হাদিস (টেস্ট করার জন্য)
        Hadith::create([
            'category_id' => $category->id,
            'sub_category_id' => null, // পরিচ্ছেদ নেই
            'hadith_number' => '৪',
            'arabic_text' => 'جَابِرَ بْنَ عَبْدِ اللَّهِ الأَنْصَارِيَّ...',
            'bangla_text' => 'জাবির ইবনু ‘আবদুল্লাহ আনসারী (রাঃ) ওহী স্থগিত হওয়া প্রসঙ্গে বর্ণনা করেন যে...',
            'english_text' => 'Jabir bin Abdullah Al-Ansari narrated...',
            'reference' => 'সহীহ বুখারী, হাদিস ৪',
            'grade' => 'সহীহ',
            'points' => 5,
        ]);
    }
}
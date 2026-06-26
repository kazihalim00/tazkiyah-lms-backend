<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HadithCategory;
use App\Models\HadithSubCategory;
use App\Models\Hadith;
use Illuminate\Support\Str;

class RealHadithSeeder extends Seeder
{
    public function run()
    {
        // ১. রিয়েল ক্যাটাগরি: ওহীর সূচনা
        $category = HadithCategory::create([
            'name_bn' => 'ওহীর সূচনা',
            'name_en' => 'Revelation',
            'slug' => Str::slug('Revelation') . '-' . time()
        ]);

        // ২. রিয়েল পরিচ্ছেদ (Sub-categories)
        $sub1 = HadithSubCategory::create([
            'category_id' => $category->id,
            'name_bn' => 'ওহীর সূচনা কীভাবে হয়েছিল',
            'name_en' => 'How the Divine Revelation started',
        ]);

        $sub2 = HadithSubCategory::create([
            'category_id' => $category->id,
            'name_bn' => 'ওহী নাজিলের সময়কার অবস্থা',
            'name_en' => 'The state of the Prophet during Revelation',
        ]);

        // ৩. সহীহ বুখারীর রিয়েল ৭টি হাদিস
        $realHadiths = [
            [
                'sub_id' => $sub1->id,
                'number' => '১',
                'arabic' => 'إِنَّمَا الأَعْمَالُ بِالنِّيَّاتِ، وَإِنَّمَا لِكُلِّ امْرِئٍ مَا نَوَى، فَمَنْ كَانَتْ هِجْرَتُهُ إِلَى دُنْيَا يُصِيبُهَا، أَوْ إِلَى امْرَأَةٍ يَنْكِحُهَا، فَهِجْرَتُهُ إِلَى مَا هَاجَرَ إِلَيْهِ',
                'bangla' => 'উমর ইবনুল খাত্তাব (রাঃ) থেকে বর্ণিত। রাসূলুল্লাহ (ﷺ) বলেছেন: "যাবতীয় কাজ নিয়তের উপর নির্ভরশীল। আর মানুষের জন্য তাই প্রাপ্য, যা সে নিয়ত করেছে। অতএব যার হিজরত হবে দুনিয়া লাভের জন্য কিংবা কোন নারীকে বিয়ে করার উদ্দেশ্যে, তার হিজরত সে উদ্দেশ্যেই গণ্য হবে।"',
                'english' => 'The reward of deeds depends upon the intentions and every person will get the reward according to what he has intended.',
                'ref' => 'সহীহ বুখারী, হাদিস ১',
            ],
            [
                'sub_id' => $sub1->id,
                'number' => '২',
                'arabic' => 'أَنَّ الْحَارِثَ بْنَ هِشَامٍ، سَأَلَ رَسُولَ اللَّهِ صلى الله عليه وسلم كَيْفَ يَأْتِيكَ الْوَحْىُ فَقَالَ رَسُولُ اللَّهِ صلى الله عليه وسلم ‏ "‏ أَحْيَانًا يَأْتِينِي مِثْلَ صَلْصَلَةِ الْجَرَسِ...',
                'bangla' => 'উম্মুল মুমিনীন আয়িশা (রাঃ) থেকে বর্ণিত। হারিস ইবনু হিশাম (রাঃ) আল্লাহর রাসূল (ﷺ)-কে জিজ্ঞেস করলেন, "হে আল্লাহর রাসূল! আপনার নিকট ওহী কীভাবে আসে?" তিনি বললেন, "কোনো কোনো সময় তা ঘণ্টা বাজার ন্যায় আমার নিকট আসে। আর এটিই আমার উপর সবচেয়ে বেশি কষ্টদায়ক হয়।"',
                'english' => 'Al-Harith bin Hisham asked Allah\'s Messenger (ﷺ) "O Allah\'s Messenger (ﷺ)! How is the Divine Inspiration revealed to you?"...',
                'ref' => 'সহীহ বুখারী, হাদিস ২',
            ],
            [
                'sub_id' => $sub1->id,
                'number' => '৩',
                'arabic' => 'أَوَّلُ مَا بُدِئَ بِهِ رَسُولُ اللَّهِ صلى الله عليه وسلم مِنَ الْوَحْىِ الرُّؤْيَا الصَّالِحَةُ فِي النَّوْمِ، فَكَانَ لاَ يَرَى رُؤْيَا إِلاَّ جَاءَتْ مِثْلَ فَلَقِ الصُّبْحِ...',
                'bangla' => 'উম্মুল মুমিনীন আয়িশা (রাঃ) থেকে বর্ণিত। তিনি বলেন, আল্লাহর রাসূল (ﷺ)-এর নিকট সর্বপ্রথম যে ওহী আসে, তা ছিল নিদ্রাবস্থায় সত্য স্বপ্নরূপে। তিনি যে স্বপ্নই দেখতেন তা ভোরের আলোর ন্যায় প্রকাশ পেত...',
                'english' => 'The commencement of the Divine Inspiration to Allah\'s Messenger (ﷺ) was in the form of good dreams...',
                'ref' => 'সহীহ বুখারী, হাদিস ৩',
            ],
            [
                'sub_id' => $sub2->id,
                'number' => '৪',
                'arabic' => 'وَهْوَ يُحَدِّثُ عَنْ فَتْرَةِ الْوَحْىِ، فَقَالَ فِي حَدِيثِهِ بَيْنَا أَنَا أَمْشِي، إِذْ سَمِعْتُ صَوْتًا مِنَ السَّمَاءِ...',
                'bangla' => 'জাবির ইবনু ‘আবদুল্লাহ আনসারী (রাঃ) ওহী স্থগিত হওয়া প্রসঙ্গে বর্ণনা করেন যে, আল্লাহর রাসূল (ﷺ) বলেছেনঃ "একদা আমি হাঁটছি, হঠাৎ আসমান হতে একটি আওয়াজ শুনতে পেলাম..."',
                'english' => 'Jabir bin Abdullah Al-Ansari while talking about the period of pause in revelation reported...',
                'ref' => 'সহীহ বুখারী, হাদিস ৪',
            ],
            [
                'sub_id' => $sub2->id,
                'number' => '৫',
                'arabic' => 'لاَ تُحَرِّكْ بِهِ لِسَانَكَ لِتَعْجَلَ بِهِ * إِنَّ عَلَيْنَا جَمْعَهُ وَقُرْآنَهُ...',
                'bangla' => 'ইবনু ‘আব্বাস (রাঃ) হতে বর্ণিত। মহান আল্লাহর বাণীঃ “ওহী দ্রুত আয়ত্ত করার জন্য আপনি ওহী নাযিল হওয়ার সময় আপনার জিহ্বা নাড়বেন না।” (সূরাহ ক্বিয়ামাহ ৭৫/১৬)...',
                'english' => 'Ibn Abbas narrated: Allah\'s Messenger (ﷺ) used to move his tongue when the Divine Inspiration was being revealed to him...',
                'ref' => 'সহীহ বুখারী, হাদিস ۵',
            ],
            [
                'sub_id' => $sub2->id,
                'number' => '৬',
                'arabic' => 'كَانَ رَسُولُ اللَّهِ صلى الله عليه وسلم أَجْوَدَ النَّاسِ، وَكَانَ أَجْوَدُ مَا يَكُونُ فِي رَمَضَانَ...',
                'bangla' => 'ইবনু ‘আব্বাস (রাঃ) হতে বর্ণিত। তিনি বলেন, আল্লাহর রাসূল (ﷺ) ছিলেন সর্বশ্রেষ্ঠ দানশীল। রমাযানে তিনি আরো অধিক দানশীল হতেন, যখন জিবরীল (আঃ) তাঁর সাথে সাক্ষাৎ করতেন...',
                'english' => 'Ibn Abbas narrated: The Prophet (ﷺ) was the most generous of all the people...',
                'ref' => 'সহীহ বুখারী, হাদিস ৬',
            ],
            [
                'sub_id' => null, // এটি সরাসরি ক্যাটাগরির আন্ডারে থাকবে (টেস্ট করার জন্য)
                'number' => '৭',
                'arabic' => 'أَنَّ أَبَا سُفْيَانَ بْنَ حَرْبٍ، أَخْبَرَهُ أَنَّ هِرَقْلَ، أَرْسَلَ إِلَيْهِ فِي رَكْبٍ مِنْ قُرَيْشٍ...',
                'bangla' => 'আবদুল্লাহ ইবনু আব্বাস (রাঃ) বর্ণনা করেন যে, আবূ সুফিয়ান ইবনু হরব তাকে বলেছেন, রাজা হিরাক্লিয়াস একদা তাঁর নিকট লোক প্রেরণ করলেন...',
                'english' => 'Abdullah bin Abbas narrated: Abu Sufyan bin Harb informed me that Heraclius had sent a messenger to him...',
                'ref' => 'সহীহ বুখারী, হাদিস ৭',
            ]
        ];

        // ডাটাবেজে সেভ করা হচ্ছে
        foreach ($realHadiths as $h) {
            Hadith::create([
                'category_id' => $category->id,
                'sub_category_id' => $h['sub_id'],
                'hadith_number' => $h['number'],
                'arabic_text' => $h['arabic'],
                'bangla_text' => $h['bangla'],
                'english_text' => $h['english'],
                'reference' => $h['ref'],
                'grade' => 'সহীহ',
                'points' => 5, 
            ]);
        }
    }
}
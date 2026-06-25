<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ayah;
use Illuminate\Support\Facades\File;

class AyahTafsirSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = database_path('data/tafsir.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("tafsir.json ফাইলটি খুঁজে পাচ্ছি না!");
            return;
        }

        $data = json_decode(File::get($jsonPath), true);

        foreach ($data as $item) {
            \App\Models\Ayah::where('surah_id', $item['surah'])
                ->where('ayah_no', $item['aya'])
                ->update(['tafsir' => $item['tafsir']]);
        }
        $this->command->info("আলহামদুলিল্লাহ! রিয়েল তাফসীর ডাটাবেজে আপডেট হয়েছে।");
    }
}
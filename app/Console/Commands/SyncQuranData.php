<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ayah;
use Illuminate\Support\Facades\Http;

class SyncQuranData extends Command
{
    protected $signature = 'quran:sync';
    protected $description = 'Sync Quran data from API';

    public function handle()
    {
        $this->info("Fetching data from API...");

        // এটি একটি ওপেন সোর্স এপিআই যা বাংলা অনুবাদ প্রদান করে
        $response = Http::get('https://api.alquran.cloud/v1/quran/bn.bengali');

        if ($response->successful()) {
            $data = $response->json()['data']['surahs'];

            foreach ($data as $surah) {
                foreach ($surah['ayahs'] as $ayahData) {
                    // ডাটাবেজে আপডেট করুন
                    Ayah::where('surah_id', $surah['number'])
                        ->where('ayah_no', $ayahData['numberInSurah'])
                        ->update(['tafsir' => $ayahData['text']]);
                }
            }
            $this->info("Alhamdulillah! All translations/tafsir synced successfully.");
        } else {
            $this->error("API request failed. Check your internet connection.");
        }
    }
}
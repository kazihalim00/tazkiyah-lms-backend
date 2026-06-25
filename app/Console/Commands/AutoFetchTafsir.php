<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ayah;
use Illuminate\Support\Facades\Http;

class AutoFetchTafsir extends Command
{
    protected $signature = 'quran:fetch-auto';
    protected $description = 'Auto fetch and populate Tafsir data from API';

    public function handle()
    {
        $this->info("Fetching data from API... please wait.");

        // এখানে আমরা একটি পাবলিক API endpoint ব্যবহার করছি
        $response = Http::get('https://api.alquran.cloud/v1/quran/bn.bengali');

        if ($response->successful()) {
            $surahs = $response->json()['data']['surahs'];

            foreach ($surahs as $surah) {
                foreach ($surah['ayahs'] as $ayahData) {
                    // সুরার নম্বর এবং আয়াত নম্বর দিয়ে ডাটাবেজ আপডেট
                    Ayah::where('surah_id', $surah['number'])
                        ->where('ayah_no', $ayahData['numberInSurah'])
                        ->update(['tafsir' => $ayahData['text']]);
                }
                $this->info("Surah " . $surah['number'] . " processed.");
            }

            $this->info("Alhamdulillah! All Ayahs synced automatically.");
        } else {
            $this->error("API Connection Failed!");
        }
    }
}
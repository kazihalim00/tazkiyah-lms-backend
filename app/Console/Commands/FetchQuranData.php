<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Surah;
use App\Models\Ayah;

class FetchQuranData extends Command
{
 
    protected $signature = 'quran:fetch';
    protected $description = 'Fetch entire Quran (Arabic, Bangla, English) from API and save to database';

    public function handle()
    {
        $this->info("Bismillah! Starting Quran data download. This might take 2-3 minutes depending on your internet speed...");


        $bar = $this->output->createProgressBar(114);
        $bar->start();

        for ($i = 1; $i <= 114; $i++) {

            $arRes = Http::timeout(120)->get("https://api.alquran.cloud/v1/surah/{$i}/quran-uthmani");
            $bnRes = Http::timeout(120)->get("https://api.alquran.cloud/v1/surah/{$i}/bn.bengali");
            $enRes = Http::timeout(120)->get("https://api.alquran.cloud/v1/surah/{$i}/en.sahih");

            if ($arRes->successful() && $bnRes->successful() && $enRes->successful()) {
                $arData = $arRes->json()['data'];
                $bnData = $bnRes->json()['data'];
                $enData = $enRes->json()['data'];

                
                $surah = Surah::updateOrCreate(
                    ['surah_no' => $arData['number']],
                    [
                        'name_arabic' => $arData['name'],
                        'name_bangla' => $arData['englishName'], 
                        'name_english' => $arData['englishNameTranslation'], 
                        'revelation_type' => $arData['revelationType'],
                        'total_ayahs' => $arData['numberOfAyahs']
                    ]
                );

    
                foreach ($arData['ayahs'] as $index => $ayah) {
                    Ayah::updateOrCreate(
                        [
                            'surah_id' => $surah->id,
                            'ayah_no' => $ayah['numberInSurah']
                        ],
                        [
                            'arabic_text' => $ayah['text'],
                            'bangla_text' => $bnData['ayahs'][$index]['text'],
                            'english_text' => $enData['ayahs'][$index]['text'],
                        ]
                    );
                }
            }
   
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nAlhamdulillah! Full Quran Data has been successfully saved to your database!");
    }
}
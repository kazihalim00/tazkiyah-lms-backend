<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Hadith;
use Illuminate\Support\Facades\DB;

class FetchHadithData extends Command
{
    protected $signature = 'hadith:fetch';
    protected $description = 'Fetch online Hadith database and save to local database';

    public function handle()
    {
        $this->info("Bismillah! Fetching Hadith data from online repository...");

        $bnUrl = "https://cdn.jsdelivr.net/gh/fawazahmed0/hadith-api@1/editions/ben-bukhari.json";
        $arUrl = "https://cdn.jsdelivr.net/gh/fawazahmed0/hadith-api@1/editions/ara-bukhari.json";

        $bnResponse = Http::timeout(250)->get($bnUrl);
        $arResponse = Http::timeout(250)->get($arUrl);

        if ($bnResponse->successful() && $arResponse->successful()) {
            $bnData = $bnResponse->json()['hadiths'];
            $arData = collect($arResponse->json()['hadiths'])->keyBy('hadithnumber');

            $total = count($bnData);
            $this->info("Total {$total} Hadiths found. Syncing to database...");

            $bar = $this->output->createProgressBar($total);
            $bar->start();

            DB::beginTransaction();
            try {
                foreach ($bnData as $bnHadith) {
                    $hNum = $bnHadith['hadithnumber'];
                    $arabicText = $arData->has($hNum) ? $arData[$hNum]['text'] : null;

                    // Extracting Grade if available
                    $grade = 'Sahih';
                    if (isset($bnHadith['grades']) && count($bnHadith['grades']) > 0) {
                        $grade = $bnHadith['grades'][0]['grade'];
                    }

                    Hadith::updateOrCreate(
                        [
                            'book_name' => 'Sahih Bukhari',
                            'hadith_number' => $hNum
                        ],
                        [
                            'arabic_text' => $arabicText,
                            'bangla_text' => $bnHadith['text'],
                            'grade' => $grade,
                            'reference' => 'Sahih al-Bukhari, Hadith No: ' . $hNum,
                            'points' => 5,
                            'narrator' => 'Companion'
                        ]
                    );

                    $bar->advance();
                }

                DB::commit();
                $bar->finish();
                $this->info("\n\nAlhamdulillah! Data synchronization complete.");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("\nDatabase Error: " . $e->getMessage());
            }
        } else {
            $this->error("Failed to connect to the online source.");
        }
    }
}
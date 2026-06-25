<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Ayah;
use Illuminate\Support\Facades\DB;

class FetchTafsir extends Command
{
    protected $signature = 'quran:fetch-tafsir';
    protected $description = 'Fetch and populate Tafsir/Translation to Ayahs table';

    public function handle()
    {
        $this->info("Bismillah! Starting Tafsir population...");

        $url = "https://cdn.jsdelivr.net/gh/fawazahmed0/quran-api@1/editions/ben-bengali.json";
        $response = Http::timeout(300)->get($url);

        if ($response->successful()) {
            $data = $response->json();
            $ayahsData = $data['quran']; 

            $bar = $this->output->createProgressBar(count($ayahsData));
            $bar->start();

            DB::beginTransaction();
            try {
                foreach ($ayahsData as $item) {
                    Ayah::where('surah_id', $item['surah'])
                        ->where('ayah_no', $item['aya'])
                        ->update(['tafsir' => $item['text']]); 
                    $bar->advance();
                }

                DB::commit();
                $bar->finish();
                $this->info("\n\nAlhamdulillah! Tafsir/Translation populated successfully.");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("\nError: " . $e->getMessage());
            }
        } else {
            $this->error("Failed to fetch data from API.");
        }
    }
}
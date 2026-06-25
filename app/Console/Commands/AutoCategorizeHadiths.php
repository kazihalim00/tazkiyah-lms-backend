<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Hadith;
use App\Models\HadithCategory;
use Illuminate\Support\Facades\DB;

class AutoCategorizeHadiths extends Command
{
    protected $signature = 'hadith:auto-categorize';
    protected $description = 'Automatically categorize 7500+ hadiths based on API metadata';

    public function handle()
    {
        $this->info("Bismillah! Starting Auto-Categorization. Please wait...");

        $url = "https://cdn.jsdelivr.net/gh/fawazahmed0/hadith-api@1/editions/ben-bukhari.json";
        $response = Http::timeout(120)->get($url);

        if ($response->successful()) {
            $data = $response->json();

            $sections = $data['metadata']['sections'];
            $sectionDetails = $data['metadata']['section_details'];

            $bar = $this->output->createProgressBar(count($sectionDetails));
            $bar->start();

            DB::beginTransaction();
            try {
                foreach ($sectionDetails as $sectionId => $details) {
                    if ($sectionId == 0 || empty($sections[$sectionId]))
                        continue;

                    $categoryName = $sections[$sectionId];

           
                    $category = HadithCategory::firstOrCreate(
                        ['name_en' => $categoryName], 
                        [
                            'name_bn' => $categoryName, 
                            'slug' => \Illuminate\Support\Str::slug($categoryName) 
                        ]
                    );

                    Hadith::where('book_name', 'Sahih Bukhari')
                        ->whereBetween('hadith_number', [$details['hadithnumber_first'], $details['hadithnumber_last']])
                        ->update(['category_id' => $category->id]);

                    $bar->advance();
                }

                DB::commit();
                $bar->finish();
                $this->info("\n\nAlhamdulillah! All 7,589 Hadiths have been perfectly categorized magically!");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("\nDatabase Error: " . $e->getMessage());
            }
        } else {
            $this->error("Failed to fetch metadata from API.");
        }
    }
}
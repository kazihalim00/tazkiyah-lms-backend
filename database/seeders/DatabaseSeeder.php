<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LmsLevel;
use App\Models\LmsModule;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create an admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create 3 LMS levels
        LmsLevel::factory(3)->create()->each(function ($level) {

            // For each level, create 3 modules
            LmsModule::factory(3)->create([
                'lms_level_id' => $level->id,
            ])->each(function ($module) {

                // For each module, create 4 lessons
                Lesson::factory(4)->create([
                    'lms_module_id' => $module->id,
                ]);
            });
        });
    }
}
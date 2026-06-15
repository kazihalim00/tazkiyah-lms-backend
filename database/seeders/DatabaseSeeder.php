<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LmsLevel;
use App\Models\LmsModule;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create an admin user for testing purposes
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create 3 LMS levels, and for each level, generate 4 modules
        LmsLevel::factory(3)->create()->each(function ($level) {
            LmsModule::factory(4)->create([
                'lms_level_id' => $level->id,
            ]);
        });
    }
}
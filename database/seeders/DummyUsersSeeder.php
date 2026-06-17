<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DummyUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Array of dummy users to populate suggestions and leaderboard
        $users = [
            ['name' => 'Adnan Chowdhury', 'email' => 'adnan@example.com', 'points' => 320],
            ['name' => 'Ahsan Habib', 'email' => 'ahsan@example.com', 'points' => 210],
            ['name' => 'Tahmid Rahman', 'email' => 'tahmid@example.com', 'points' => 150],
            ['name' => 'Tanvir Ahmed', 'email' => 'tanvir@example.com', 'points' => 90],
        ];

        foreach ($users as $u) {
            // Using updateOrCreate to prevent unique email constraint violations
            User::updateOrCreate(
                ['email' => $u['email']], // Unique identifier condition
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'gender' => 'male', // Strictly matches to show in suggestions
                    'total_points' => $u['points'],
                    'role' => 'user',
                    'is_admin' => 0
                ]
            );
        }
    }
}
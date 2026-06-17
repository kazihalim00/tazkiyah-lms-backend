<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // এটি তোমার বর্তমান অ্যাকাউন্টকে আপডেট করে পাসওয়ার্ডসহ অ্যাডমিন বানিয়ে দেবে
        User::updateOrCreate(
            ['email' => 'kaziabdulhalimsunny01@gmail.com'],
            [
                'name' => 'Kazi Abdul Halim Sunny',
                'password' => Hash::make('12345678'),
                'is_admin' => 1,
                'role' => 'admin',
                'gender' => 'male',
                'total_points' => 0
            ]
        );
    }
}
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LmsLevelFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Generate a random Islamic level title
            'title' => $this->faker->randomElement(['Aqeedah Basics', 'Salah Rules', 'Seerah of Prophet', 'Tazkiyah']),
            // Generate a dummy sentence for the description
            'description' => $this->faker->sentence(),
        ];
    }
}
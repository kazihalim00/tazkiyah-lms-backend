<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LmsModuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Generate a dummy module title with 3 random words
            'title' => 'Module: ' . $this->faker->words(3, true),
            // Assign a random order number between 1 and 5
            'order' => $this->faker->numberBetween(1, 5),
        ];
    }
}
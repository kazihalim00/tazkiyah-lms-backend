<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    public function definition(): array
    {
        // Randomly choose a content type
        $type = $this->faker->randomElement(['text', 'video', 'quiz']);

        return [
            // Generate a dummy lesson title
            'title' => 'Lesson: ' . $this->faker->words(3, true),
            'content_type' => $type,

            // If type is text, generate dummy paragraphs, otherwise leave null
            'content_body' => $type === 'text' ? $this->faker->paragraphs(2, true) : null,

            // If type is video, generate a dummy URL, otherwise leave null
            'media_url' => $type === 'video' ? 'https://www.youtube.com/watch?v=dummy' : null,

            // Assign a random order number
            'order' => $this->faker->numberBetween(1, 5),
        ];
    }
}
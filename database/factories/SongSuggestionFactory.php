<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SongSuggestion>
 */
class SongSuggestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'youtube' => fake()->optional()->url(),
            'description' => fake()->optional()->paragraph(),
            'song_writer' => fake()->optional()->name(),
            'lyrics' => fake()->paragraphs(3, true),
            'music_notes' => fake()->optional()->paragraph(),
            'status' => 'pending',
        ];
    }
}

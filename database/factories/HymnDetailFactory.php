<?php

namespace Database\Factories;

use App\Models\Hymn;
use App\Models\HymnBook;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HymnDetail>
 */
class HymnDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hymn_book_id' => HymnBook::factory(),
            'no' => fake()->numberBetween(1, 500),
            'hymn_id' => Hymn::factory(),
            'title' => fake()->sentence(),
            'key' => fake()->randomElement(['C', 'D', 'E', 'F', 'G', 'A', 'B', 'Db', 'Eb', 'Gb', 'Ab', 'Bb']),
            'scriptures' => fake()->sentences(3),
            'lyrics' => fake()->paragraphs(5),
            'pdf_file' => fake()->url(),
            'pptx_file' => fake()->url(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\HymnCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hymn>
 */
class HymnFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'composer' => fake()->name(),
            'hymn_category_id' => HymnCategory::factory(),
        ];
    }
}

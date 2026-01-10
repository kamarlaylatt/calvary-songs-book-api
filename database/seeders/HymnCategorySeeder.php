<?php

namespace Database\Seeders;

use App\Models\HymnCategory;
use Illuminate\Database\Seeder;

class HymnCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HymnCategory::factory()->count(10)->create();
    }
}

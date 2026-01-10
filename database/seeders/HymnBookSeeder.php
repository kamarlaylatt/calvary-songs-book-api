<?php

namespace Database\Seeders;

use App\Models\HymnBook;
use Illuminate\Database\Seeder;

class HymnBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HymnBook::factory()->count(5)->create();
    }
}

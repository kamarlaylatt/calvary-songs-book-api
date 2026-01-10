<?php

namespace Database\Seeders;

use App\Models\Hymn;
use Illuminate\Database\Seeder;

class HymnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Hymn::factory()->count(50)->create();
    }
}

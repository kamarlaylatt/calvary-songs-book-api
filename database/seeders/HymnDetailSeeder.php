<?php

namespace Database\Seeders;

use App\Models\HymnDetail;
use Illuminate\Database\Seeder;

class HymnDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HymnDetail::factory()->count(100)->create();
    }
}

<?php

namespace Database\Seeders;

use App\Models\Style;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StyleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Style::truncate();
        // \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $styles = [
            ['name' => 'Country'],
            ['name' => 'Slow Go Go'],
            ['name' => 'Rock'],
            ['name' => 'Blues'],
            ['name' => 'House'],
        ];

        foreach ($styles as $style) {
            Style::create($style);
        }
    }
}

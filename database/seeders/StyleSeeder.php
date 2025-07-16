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
        $styles = [
            ['name' => 'Worship'],
            ['name' => 'Praise'],
            ['name' => 'Hymn'],
            ['name' => 'Gospel'],
            ['name' => 'Instrumental'],
        ];

        foreach ($styles as $style) {
            Style::create($style);
        }
    }
}
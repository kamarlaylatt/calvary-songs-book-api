<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SongLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('song_languages')->insert([
            ['name' => 'English', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Myanmar', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

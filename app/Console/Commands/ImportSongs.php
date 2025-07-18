<?php

namespace App\Console\Commands;

use App\Models\Song;
use App\Models\Style;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportSongs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-songs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import songs from a CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Importing songs...');

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Song::truncate();
        Style::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $csvFile = fopen(base_path("songs.csv"), "r");

        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                $style = Style::firstOrCreate(['name' => $data[3]]);
                $code = Song::max('code') + 1;

                Song::create([
                    "code" => $code,
                    "title" => $data[0],
                    "slug" => Str::slug($data[0]) . '-' . $code,
                    "youtube" => $data[1],
                    "song_writer" => $data[2],
                    "style_id" => $style->id,
                    "key" => $data[4],
                    "lyrics" => $data[5],
                    "music_notes" => $data[6],
                    'createable_id' => 1,
                    'createable_type' => 'App\\Models\\Admin',
                ]);
            }
            $firstline = false;
        }

        fclose($csvFile);

        $this->info('Songs imported successfully!');
    }
}

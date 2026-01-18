<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Hymn;
use App\Models\HymnCategory;
use App\Models\Song;
use App\Models\Style;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncHymnsFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:hymns {csv-path : Path to the CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync hymn data from CSV to database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $csvPath = $this->argument('csv-path');

        if (! file_exists($csvPath)) {
            $this->error("CSV file not found: {$csvPath}");

            return self::FAILURE;
        }

        $this->info('Starting hymn sync from CSV...');
        $this->info("Reading file: {$csvPath}");

        // Step 1: Create or get "Hymns" category
        $this->info("\n=== Step 1: Creating/Getting 'Hymns' Category ===");
        $hymnsCategory = Category::firstOrCreate(
            ['slug' => 'hymns'],
            [
                'name' => 'Hymns',
                'description' => 'Hymn Songs',
            ]
        );
        $this->info("✓ Category ID: {$hymnsCategory->id} - {$hymnsCategory->name}");

        // Step 2: Get or create Style for hymns
        $this->info("\n=== Step 2: Getting/Creating Hymn Style ===");
        $hymnStyle = Style::firstOrCreate(
            ['name' => 'Hymn'],
            ['name' => 'Hymn']
        );
        $this->info("✓ Style ID: {$hymnStyle->id} - {$hymnStyle->name}");

        // Step 3: Get Admin user (ID = 1)
        $this->info("\n=== Step 3: Getting Admin User ===");
        $admin = Admin::find(1);
        if (! $admin) {
            $this->error('Admin user with ID 1 not found. Please create an admin first.');

            return self::FAILURE;
        }
        $this->info("✓ Admin ID: {$admin->id} - {$admin->name}");

        // Step 4: Parse CSV and sync data
        $this->info("\n=== Step 4: Parsing CSV and Syncing Data ===");

        $csvData = $this->parseCsv($csvPath);
        $totalRows = count($csvData);
        $this->info("Found {$totalRows} rows in CSV");

        $progressBar = $this->output->createProgressBar($totalRows);
        $progressBar->start();

        $stats = [
            'songs_created' => 0,
            'songs_updated' => 0,
            'hymn_categories_created' => 0,
            'hymns_created' => 0,
            'hymns_updated' => 0,
            'skipped' => 0,
        ];

        foreach ($csvData as $index => $row) {
            try {
                $referenceId = $this->parseCsvField($row['Reference ID'] ?? '');
                $hymnNumber = $this->parseCsvField($row['Hymn Number'] ?? '');
                $titleMm = $this->parseCsvField($row['Title (Myanmar)'] ?? '');
                $titleEn = $this->parseCsvField($row['Title (English)'] ?? '');
                $composer = $this->parseCsvField($row['Composer'] ?? '');
                $categoryName = $this->parseCsvField($row['Category'] ?? '');
                $lyrics = $this->parseCsvField($row['Lyrics'] ?? '');

                // Skip if title is empty
                if (empty($titleMm)) {
                    $stats['skipped']++;
                    $progressBar->advance();

                    continue;
                }

                // Create or get song
                $song = $this->createOrUpdateSong(
                    $hymnNumber,
                    $titleMm,
                    $composer,
                    $lyrics,
                    $hymnsCategory->id,
                    $hymnStyle->id,
                    $admin,
                    $stats
                );

                // Create or get hymn category
                $hymnCategoryId = null;
                if (! empty($categoryName)) {
                    $hymnCategoryId = $this->createOrUpdateHymnCategory($categoryName, $stats);
                }

                // Create or update hymn
                $this->createOrUpdateHymn(
                    $hymnNumber,
                    $referenceId,
                    $titleEn,
                    $hymnCategoryId,
                    $song->id,
                    $stats
                );

            } catch (\Exception $e) {
                $this->newLine();
                $this->warn("Row {$index}: Error - {$e->getMessage()}");
                $stats['skipped']++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display summary
        $this->info('=== SYNC SUMMARY ===');
        $this->info("Songs created: {$stats['songs_created']}");
        $this->info("Songs updated: {$stats['songs_updated']}");
        $this->info("Hymn categories created: {$stats['hymn_categories_created']}");
        $this->info("Hymns created: {$stats['hymns_created']}");
        $this->info("Hymns updated: {$stats['hymns_updated']}");
        $this->info("Skipped: {$stats['skipped']}");

        $this->info("\n✓ Sync completed successfully!");

        return self::SUCCESS;
    }

    /**
     * Parse CSV file into array of associative arrays
     */
    private function parseCsv(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return [];
        }

        $headers = fgetcsv($handle, 0, ',');
        if ($headers === false) {
            fclose($handle);

            return [];
        }

        // Clean headers (remove BOM if present)
        $headers = array_map(fn ($h) => preg_replace('/^\xEF\xBB\xBF/', '', $h), $headers);

        $data = [];
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if (count($headers) === count($row)) {
                $data[] = array_combine($headers, $row);
            }
        }

        fclose($handle);

        return $data;
    }

    /**
     * Parse CSV field (remove surrounding quotes and trim whitespace)
     */
    private function parseCsvField(string $field): string
    {
        // Remove surrounding quotes if present
        $field = trim($field);
        if (str_starts_with($field, '"') && str_ends_with($field, '"')) {
            $field = substr($field, 1, -1);
        }

        return $field;
    }

    /**
     * Create or update song
     */
    private function createOrUpdateSong(
        string $code,
        string $title,
        string $songWriter,
        string $lyrics,
        int $categoryId,
        int $styleId,
        Admin $admin,
        array &$stats
    ): Song {
        // Generate slug from title
        $slug = Str::slug($title).'-'.$code;

        // Check if song exists by code and title
        $song = Song::where('code', $code)->first();

        if (! $song) {
            // Get next code from existing songs
            $maxCode = Song::max('code');
            $nextCode = is_null($maxCode) ? 1 : (int) $maxCode + 1;

            $song = Song::create([
                'code' => $nextCode,
                'title' => $title,
                'slug' => $slug,
                'song_writer' => $songWriter,
                'lyrics' => $lyrics,
                'style_id' => $styleId,
                'popular_rating' => 3,
                'createable_type' => Admin::class,
                'createable_id' => $admin->id,
            ]);
            $song->categories()->attach($categoryId);
            $stats['songs_created']++;
        } else {
            // Update existing song
            $song->update([
                'title' => $title,
                'slug' => $slug,
                'song_writer' => $songWriter,
                'lyrics' => $lyrics,
            ]);
            // Sync category
            $song->categories()->syncWithoutDetaching([$categoryId]);
            $stats['songs_updated']++;
        }

        return $song;
    }

    /**
     * Create or update hymn category
     */
    private function createOrUpdateHymnCategory(string $name, array &$stats): int
    {
        $hymnCategory = HymnCategory::firstOrCreate(
            ['name' => $name],
            ['name' => $name]
        );

        if ($hymnCategory->wasRecentlyCreated) {
            $stats['hymn_categories_created']++;
        }

        return $hymnCategory->id;
    }

    /**
     * Create or update hymn
     */
    private function createOrUpdateHymn(
        int $no,
        int $referenceId,
        string $englishTitle,
        ?int $hymnCategoryId,
        int $songId,
        array &$stats
    ): void {
        $hymn = Hymn::where('reference_id', $referenceId)->first();

        if (! $hymn) {
            Hymn::create([
                'no' => $no,
                'hymn_category_id' => $hymnCategoryId,
                'song_id' => $songId,
                'reference_id' => $referenceId,
                'english_title' => $englishTitle,
            ]);
            $stats['hymns_created']++;
        } else {
            $hymn->update([
                'no' => $no,
                'hymn_category_id' => $hymnCategoryId,
                'english_title' => $englishTitle,
            ]);
            $stats['hymns_updated']++;
        }
    }
}

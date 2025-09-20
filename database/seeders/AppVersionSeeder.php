<?php

namespace Database\Seeders;

use App\Models\AppVersion;
use Illuminate\Database\Seeder;

class AppVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed only version 1 for both platforms
        AppVersion::create([
            'platform' => 'android',
            'version_code' => 1,
            'version_name' => '1.0.0',
            'update_url' => 'https://play.google.com/store/apps/details?id=com.example.calvarysongs',
            'release_notes' => 'Initial release',
            'is_force_update' => false,
        ]);

        // iOS version 1 (for future use)
        AppVersion::create([
            'platform' => 'ios',
            'version_code' => 1,
            'version_name' => '1.0.0',
            'update_url' => 'https://apps.apple.com/app/id123456789',
            'release_notes' => 'Initial iOS release',
            'is_force_update' => false,
        ]);
    }
}

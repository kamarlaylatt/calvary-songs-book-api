<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('song_category')) {
            Schema::rename('song_category', 'category_song');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('category_song')) {
            Schema::rename('category_song', 'song_category');
        }
    }
};

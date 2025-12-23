<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip fulltext indexes for SQLite as they're not supported
        if (DB::connection()->getDriverName() !== 'sqlite') {
            Schema::table('songs', function (Blueprint $table) {
                $table->fullText(['title']);
                $table->fullText(['song_writer']);
                $table->fullText(['lyrics']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip fulltext indexes for SQLite as they're not supported
        if (DB::connection()->getDriverName() !== 'sqlite') {
            Schema::table('songs', function (Blueprint $table) {
                $table->dropFullText(['title']);
                $table->dropFullText(['song_writer']);
                $table->dropFullText(['lyrics']);
            });
        }
    }
};

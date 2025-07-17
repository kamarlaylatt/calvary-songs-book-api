<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->fullText(['title']);
            $table->fullText(['song_writer']);
            $table->fullText(['lyrics']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropFullText(['title']);
            $table->dropFullText(['song_writer']);
            $table->dropFullText(['lyrics']);
        });
    }
};

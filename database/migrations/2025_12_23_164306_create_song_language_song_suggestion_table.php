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
        Schema::create('song_language_song_suggestion', function (Blueprint $table) {
            $table->foreignId('song_language_id')->constrained()->onDelete('cascade');
            $table->foreignId('song_suggestion_id')->constrained()->onDelete('cascade');
            $table->primary(['song_language_id', 'song_suggestion_id'], 'sl_ss_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_language_song_suggestion');
    }
};

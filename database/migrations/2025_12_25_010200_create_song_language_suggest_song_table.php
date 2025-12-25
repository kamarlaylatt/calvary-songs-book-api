<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('song_language_suggest_song', function (Blueprint $table) {
            $table->unsignedBigInteger('song_language_id');
            $table->unsignedBigInteger('suggest_song_id');

            $table->primary(['song_language_id', 'suggest_song_id'], 'sl_ss_primary');

            $table->foreign('song_language_id')
                ->references('id')->on('song_languages')
                ->onDelete('cascade');

            $table->foreign('suggest_song_id')
                ->references('id')->on('suggest_songs')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_language_suggest_song');
    }
};

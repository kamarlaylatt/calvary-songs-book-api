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
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('youtube')->nullable();
            $table->text('description')->nullable();
            $table->string('song_writer')->nullable();
            $table->foreignId('style_id')->constrained()->onDelete('cascade');
            $table->text('lyrics');
            $table->text('music_notes')->nullable();
            $table->morphs('createable');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};

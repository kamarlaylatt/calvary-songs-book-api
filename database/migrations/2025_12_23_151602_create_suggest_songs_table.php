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
        Schema::create('suggest_songs', function (Blueprint $table) {
            $table->id();
            $table->integer('code');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('youtube')->nullable();
            $table->text('description')->nullable();
            $table->string('song_writer')->nullable();
            $table->foreignId('style_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('key')->nullable();
            $table->text('lyrics');
            $table->text('music_notes')->nullable();
            $table->unsignedTinyInteger('popular_rating')->default(0);
            $table->nullableMorphs('createable');
            $table->string('email')->nullable();
            $table->unsignedTinyInteger('status')->default(1)->comment('0: cancel, 1: pending, 2: approve');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggest_songs');
    }
};

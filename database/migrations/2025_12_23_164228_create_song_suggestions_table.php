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
        Schema::create('song_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('youtube')->nullable();
            $table->text('description')->nullable();
            $table->string('song_writer')->nullable();
            $table->foreignId('style_id')->nullable()->constrained()->onDelete('set null');
            $table->text('lyrics');
            $table->text('music_notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'cancelled'])->default('pending');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_suggestions');
    }
};

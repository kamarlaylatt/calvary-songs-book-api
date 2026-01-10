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
        Schema::create('hymn_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hymn_book_id')->constrained()->cascadeOnDelete();
            $table->integer('no');
            $table->foreignId('hymn_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('key')->nullable();
            $table->text('scriptures')->nullable();
            $table->text('lyrics')->nullable();
            $table->text('pdf_file')->nullable();
            $table->text('pptx_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hymn_details');
    }
};

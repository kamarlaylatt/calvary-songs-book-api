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
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->string('platform'); // android, ios
            $table->integer('version_code');
            $table->string('version_name');
            $table->text('update_url');
            $table->text('release_notes')->nullable();
            $table->boolean('is_force_update')->default(false);
            $table->timestamps();

            $table->index(['platform', 'version_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};

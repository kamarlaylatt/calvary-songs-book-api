<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suggest_songs', function (Blueprint $table) {
            $table->dropUnique('suggest_songs_slug_unique');
            $table->dropColumn(['code', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('suggest_songs', function (Blueprint $table) {
            $table->integer('code')->after('id');
            $table->string('slug')->unique()->after('title');
        });
    }
};

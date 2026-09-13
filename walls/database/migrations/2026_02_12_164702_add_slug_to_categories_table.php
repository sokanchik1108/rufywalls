<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->unique()->after('name');
        });
    }

    public function down(): void
    {
        // Сначала удаляем индекс, который использует slug
        DB::statement('DROP INDEX IF EXISTS categories_slug_unique');

        // Только после этого удаляем колонку
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
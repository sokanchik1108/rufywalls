<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('variant_companions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')
                  ->constrained('variants')
                  ->onDelete('cascade');
            $table->foreignId('companion_variant_id')
                  ->constrained('variants')
                  ->onDelete('cascade');
            
            // уникальная пара
            $table->unique(['variant_id', 'companion_variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variant_companions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('write_off_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('write_off_id')
                ->constrained('write_offs')
                ->cascadeOnDelete();

            $table->foreignId('variant_id')
                ->constrained('variants')
                ->cascadeOnDelete();

            $table->unsignedInteger('quantity');

            $table->timestamps();

            $table->index([
                'write_off_id',
                'variant_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('write_off_items');
    }
};
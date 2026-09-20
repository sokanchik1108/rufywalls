<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fifo_allocations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_movement_id')
                ->constrained('stock_movements')
                ->cascadeOnDelete();

            $table->foreignId('inventory_layer_id')
                ->constrained('inventory_layers')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');

            $table->timestamps();

            $table->index([
                'stock_movement_id',
                'inventory_layer_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fifo_allocations');
    }
};
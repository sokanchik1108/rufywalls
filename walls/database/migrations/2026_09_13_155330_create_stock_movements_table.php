<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('warehouse_id')
                ->constrained('warehouses')
                ->cascadeOnDelete();

            $table->foreignId('variant_id')
                ->constrained('variants')
                ->cascadeOnDelete();

            $table->foreignId('batch_id')
                ->constrained('batches')
                ->restrictOnDelete();

            /*
             * receipt
             * sale
             * writeoff
             * return
             */
            $table->string('type');

            /*
             * Например:
             * receipt_items.id
             * order_items.id
             * writeoff id
             */
            $table->unsignedBigInteger('source_id')->nullable();

            $table->unsignedInteger('quantity');

            /*
             * Положительное движение:
             * receipt / return
             *
             * Отрицательное:
             * sale / writeoff
             */
            $table->smallInteger('direction');

            $table->dateTime('movement_date');

            $table->timestamps();

            $table->index([
                'warehouse_id',
                'variant_id',
                'movement_date'
            ]);

            $table->index([
                'type',
                'source_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_layers', function (Blueprint $table) {
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
             * initial = старый остаток
             * receipt = товар из приёмки
             */
            $table->string('source_type');

            /*
             * Для receipt здесь хранится receipt_items.id.
             * Для initial будет NULL.
             */
            $table->unsignedBigInteger('source_id')->nullable();

            /*
             * Количество товара, которое было
             * сформировано этим слоем.
             */
            $table->unsignedInteger('quantity');

            /*
             * Для receipt:
             * фиксированная цена из receipt_items.
             *
             * Для initial:
             * NULL.
             *
             * Старый остаток всегда берёт
             * текущий products.purchase_price.
             */
            $table->decimal('unit_cost', 12, 2)->nullable();

            /*
             * Дата, по которой работает FIFO.
             */
            $table->dateTime('layer_date');

            $table->timestamps();

            $table->index([
                'warehouse_id',
                'variant_id',
                'batch_id'
            ]);

            $table->index([
                'source_type',
                'source_id'
            ]);

            $table->index([
                'warehouse_id',
                'variant_id',
                'layer_date'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_layers');
    }
};
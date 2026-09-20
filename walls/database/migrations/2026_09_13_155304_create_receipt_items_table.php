<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipt_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('receipt_id')
                ->constrained('receipts')
                ->cascadeOnDelete();

            $table->foreignId('variant_id')
                ->constrained('variants')
                ->cascadeOnDelete();

            $table->foreignId('batch_id')
                ->constrained('batches')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');

            $table->decimal('purchase_price', 12, 2);

            $table->timestamps();

            $table->index([
                'variant_id',
                'batch_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_items');
    }
};
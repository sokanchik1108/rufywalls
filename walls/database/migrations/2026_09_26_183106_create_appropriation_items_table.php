<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appropriation_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('appropriation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('variant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('batch_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('quantity');

            $table->decimal('purchase_price', 12, 2)
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appropriation_items');
    }
};
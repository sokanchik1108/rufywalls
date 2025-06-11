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
// database/migrations/xxxx_xx_xx_create_batches_table.php
Schema::create('batches', function (Blueprint $table) {
    $table->id();
    $table->foreignId('variant_id')->constrained()->onDelete('cascade');
    $table->integer('stock');
    $table->string('batch_code');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};

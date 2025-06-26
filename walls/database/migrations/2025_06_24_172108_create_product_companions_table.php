<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_companions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('companion_id')->constrained('products')->onDelete('cascade');
            $table->unique(['product_id', 'companion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_companions');
    }
};

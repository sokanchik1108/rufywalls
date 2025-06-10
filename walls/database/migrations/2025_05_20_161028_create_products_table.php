<?php

// Миграция для продуктов
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country');
            $table->string('party');
            $table->string('sticking');
            $table->string('material');
            $table->string('purchase_price');
            $table->decimal('sale_price', 10, 2);
            $table->string('brand');
            $table->string('description');
            $table->text('detailed');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/xxxx_xx_xx_create_sales_table.php
public function up()
{
    Schema::create('sales', function (Blueprint $table) {
        $table->id();
        $table->string('sku'); // артикул
        $table->decimal('price', 10, 2); // цена продажи
        $table->integer('quantity'); // количество
        $table->decimal('total', 10, 2); // общая сумма = price * quantity
        $table->date('sale_date'); // дата продажи
        $table->string('payment_method')->default('нал');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

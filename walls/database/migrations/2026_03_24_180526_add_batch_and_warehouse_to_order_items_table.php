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
        Schema::table('order_items', function (Blueprint $table) {
            // Сохраняем код партии на момент создания заказа
            $table->string('batch_code')->nullable();

            // Сохраняем название склада на момент создания заказа
            $table->string('warehouse_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('batch_code');
            $table->dropColumn('warehouse_name');
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_settings', function (Blueprint $table) {
            $table->id();

            // Текущая сумма денег, находящихся на руках
            $table->decimal('cash_on_hand', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_settings');
    }
};

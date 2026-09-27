<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('receipt_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained('receipts')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->nullable();
            $table->text('comment')->nullable();
            $table->date('paid_at');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('receipt_payments');
    }
};

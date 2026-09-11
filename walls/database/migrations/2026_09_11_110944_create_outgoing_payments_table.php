<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outgoing_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('expense_type_id')
                ->constrained('expense_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outgoing_payments');
    }
};


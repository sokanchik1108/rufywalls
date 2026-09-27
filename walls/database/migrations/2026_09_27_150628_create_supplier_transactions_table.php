<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnDelete();

            /*
             * opening_debt — начальный долг
             * payment      — погашение долга
             * adjustment   — ручная корректировка
             */
            $table->string('type');

            /*
             * Для opening_debt и adjustment:
             * положительное число = увеличить долг
             * отрицательное число = уменьшить долг
             *
             * Для payment:
             * всегда положительное число,
             * контроллер сам уменьшает долг.
             */
            $table->decimal('amount', 15, 2);

            $table->date('transaction_date');

            $table->text('comment')->nullable();

            $table->timestamps();

            $table->index([
                'supplier_id',
                'transaction_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_transactions');
    }
};


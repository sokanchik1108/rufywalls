<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_entries', function (Blueprint $table) {
            $table->id();

            // personal_expense — личный расход руководителя
            $table->string('type');

            $table->decimal('amount', 15, 2);

            $table->date('entry_date');

            $table->text('comment')->nullable();

            $table->timestamps();

            $table->index(['type', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_entries');
    }
};


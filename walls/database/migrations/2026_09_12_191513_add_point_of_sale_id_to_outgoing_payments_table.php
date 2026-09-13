<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outgoing_payments', function (Blueprint $table) {
            $table->foreignId('point_of_sale_id')
                ->nullable()
                ->after('expense_type_id')
                ->constrained('points_of_sale')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('outgoing_payments', function (Blueprint $table) {
            $table->dropForeign(['point_of_sale_id']);
            $table->dropColumn('point_of_sale_id');
        });
    }
};
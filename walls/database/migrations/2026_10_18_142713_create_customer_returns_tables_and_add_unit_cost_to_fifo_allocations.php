
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fifo_allocations', function (Blueprint $table) {
            $table->decimal('unit_cost', 12, 2)->nullable();
        });

        Schema::create('customer_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('return_date');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'return_date']);
        });

        Schema::create('customer_return_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_return_id')
                ->constrained('customer_returns')
                ->cascadeOnDelete();

            $table->foreignId('order_item_id')
                ->constrained('order_items')
                ->restrictOnDelete();

            $table->foreignId('original_fifo_allocation_id')
                ->constrained('fifo_allocations')
                ->restrictOnDelete();

            $table->foreignId('warehouse_id')
                ->constrained('warehouses')
                ->restrictOnDelete();

            $table->foreignId('variant_id')
                ->constrained('variants')
                ->restrictOnDelete();

            $table->foreignId('batch_id')
                ->constrained('batches')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');
            $table->decimal('unit_cost', 12, 2);
            $table->timestamps();

            $table->index(['order_item_id', 'original_fifo_allocation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_return_items');
        Schema::dropIfExists('customer_returns');

        Schema::table('fifo_allocations', function (Blueprint $table) {
            $table->dropColumn('unit_cost');
        });
    }
};

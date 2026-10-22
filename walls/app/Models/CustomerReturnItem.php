<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerReturnItem extends Model
{
    protected $fillable = [
        'customer_return_id',
        'order_item_id',
        'original_fifo_allocation_id',
        'warehouse_id',
        'variant_id',
        'batch_id',
        'quantity',
        'unit_cost',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
    ];

    public function customerReturn()
    {
        return $this->belongsTo(CustomerReturn::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function originalAllocation()
    {
        return $this->belongsTo(
            FifoAllocation::class,
            'original_fifo_allocation_id'
        );
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}

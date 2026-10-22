<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FifoAllocation extends Model
{
    protected $fillable = [
        'stock_movement_id',
        'inventory_layer_id',
        'quantity',
        'unit_cost',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
    ];

    public function movement()
    {
        return $this->belongsTo(
            StockMovement::class,
            'stock_movement_id'
        );
    }

    public function layer()
    {
        return $this->belongsTo(
            InventoryLayer::class,
            'inventory_layer_id'
        );
    }
}
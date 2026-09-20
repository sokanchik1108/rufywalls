<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FifoAllocation extends Model
{
    protected $fillable = [
        'stock_movement_id',
        'inventory_layer_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
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
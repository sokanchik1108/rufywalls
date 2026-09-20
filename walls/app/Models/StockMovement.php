<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'warehouse_id',
        'variant_id',
        'batch_id',
        'type',
        'source_id',
        'quantity',
        'direction',
        'movement_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'direction' => 'integer',
        'movement_date' => 'datetime',
    ];

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

    public function allocations()
    {
        return $this->hasMany(FifoAllocation::class);
    }
}
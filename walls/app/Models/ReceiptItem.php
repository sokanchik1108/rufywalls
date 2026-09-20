<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptItem extends Model
{
    protected $fillable = [
        'receipt_id',
        'variant_id',
        'batch_id',
        'quantity',
        'purchase_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'purchase_price' => 'decimal:2',
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function inventoryLayer()
    {
        return $this->hasOne(
            InventoryLayer::class,
            'source_id'
        )->where('source_type', 'receipt');
    }
}
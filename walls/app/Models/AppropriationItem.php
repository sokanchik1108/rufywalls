<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppropriationItem extends Model
{
    protected $fillable = [
        'appropriation_id',
        'variant_id',
        'batch_id',
        'quantity',
        'purchase_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'purchase_price' => 'decimal:2',
    ];

    public function appropriation()
    {
        return $this->belongsTo(
            Appropriation::class
        );
    }

    public function variant()
    {
        return $this->belongsTo(
            Variant::class
        );
    }

    public function batch()
    {
        return $this->belongsTo(
            Batch::class
        );
    }
}
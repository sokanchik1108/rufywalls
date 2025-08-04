<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $fillable = ['product_id', 'color', 'sku', 'stock', 'images'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function getTotalStockAttribute()
    {
        return $this->batches
            ->flatMap(fn($batch) => $batch->warehouses)
            ->sum('pivot.quantity');
    }
    
}

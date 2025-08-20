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

    public function companions()
    {
        return $this->belongsToMany(
            Variant::class,
            'variant_companions',
            'variant_id',
            'companion_variant_id'
        );
    }

    public function companionOf()
{
    return $this->belongsToMany(Variant::class, 'variant_companions', 'companion_variant_id', 'variant_id');
}


    // Чтобы получить все связи сразу
    public function allCompanions()
    {
        return $this->companions->merge($this->companionOf);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = ['variant_id', 'stock', 'batch_code'];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'batch_warehouse')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function getTotalStockAttribute()
    {
        return $this->warehouses()->sum('quantity');
    }

    
}

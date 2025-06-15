<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'variant_id', 'quantity', 'price', 'image'];


    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
}

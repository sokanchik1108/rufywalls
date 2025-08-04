<?php

// app/Models/Sale.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
   protected $fillable = [
      'sku',
      'price',
      'quantity',
      'total',
      'sale_date',
      'payment_method',
      'warehouse_id',
      'batch_id', // 👈 обязательно
   ];


   protected $casts = [
      'sale_date' => 'date',
   ];

   public function warehouse()
   {
      return $this->belongsTo(Warehouse::class);
   }

   public function variant()
   {
      return $this->belongsTo(Variant::class, 'sku', 'sku');
   }

   public function batch()
   {
      return $this->belongsTo(\App\Models\Batch::class);
   }
}

<?php

// app/Models/Sale.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
   protected $fillable = ['sku', 'price', 'quantity', 'total', 'sale_date', 'payment_method'];

   protected $casts = [
      'sale_date' => 'date',
   ];
}

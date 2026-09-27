<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $fillable = ['supplier_id', 'amount', 'payment_method', 'comment', 'paid_at',];
    protected $casts = ['amount' => 'decimal:2', 'paid_at' => 'date',];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

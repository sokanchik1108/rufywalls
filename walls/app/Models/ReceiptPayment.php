<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptPayment extends Model
{
    protected $fillable = ['receipt_id', 'amount', 'payment_method', 'comment', 'paid_at',];
    protected $casts = ['amount' => 'decimal:2', 'paid_at' => 'date',];
    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}

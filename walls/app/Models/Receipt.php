<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = ['warehouse_id', 'supplier_id', 'receipt_date', 'comment',];
    protected $casts = ['receipt_date' => 'date',];
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function items()
    {
        return $this->hasMany(ReceiptItem::class);
    }
    public function payments()
    {
        return $this->hasMany(ReceiptPayment::class);
    }
    public function getTotalAmountAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->purchase_price;
        });
    }
    public function getPaidAmountAttribute()
    {
        return $this->payments->sum('amount');
    }
    public function getDebtAmountAttribute()
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }
}

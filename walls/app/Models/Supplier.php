<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'comment',
    ];

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function transactions()
    {
        return $this->hasMany(SupplierTransaction::class);
    }

    public function getDebtAttribute()
    {
        $receiptDebt = $this->receipts()
            ->with('payments')
            ->get()
            ->sum(function ($receipt) {
                $total = $receipt->items->sum(function ($item) {
                    return $item->quantity * $item->purchase_price;
                });

                $paid = $receipt->payments->sum('amount');

                return max(0, $total - $paid);
            });

        $supplierPayments = $this->payments()->sum('amount');

        return max(0, $receiptDebt - $supplierPayments);
    }
}

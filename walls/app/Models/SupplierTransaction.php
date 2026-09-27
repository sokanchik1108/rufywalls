<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierTransaction extends Model
{
    protected $fillable = [
        'supplier_id',
        'type',
        'amount',
        'transaction_date',
        'comment',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

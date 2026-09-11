<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutgoingPayment extends Model
{
    protected $fillable = [
        'expense_type_id',
        'payment_date',
        'amount',
        'description',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialEntry extends Model
{
    protected $fillable = [
        'type',
        'amount',
        'entry_date',
        'comment',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'entry_date' => 'date',
    ];
}

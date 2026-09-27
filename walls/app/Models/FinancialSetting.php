<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialSetting extends Model
{
    protected $fillable = [
        'cash_on_hand',
    ];

    protected $casts = [
        'cash_on_hand' => 'decimal:2',
    ];
}


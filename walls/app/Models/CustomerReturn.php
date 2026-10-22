<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerReturn extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'return_date',
        'comment',
    ];

    protected $casts = [
        'return_date' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(CustomerReturnItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CustomerReturnPayment::class);
    }
}

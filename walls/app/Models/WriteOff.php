<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WriteOff extends Model
{
    protected $fillable = [
        'warehouse_id',
        'writeoff_date',
        'comment',
    ];

    protected $casts = [
        'writeoff_date' => 'date',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(WriteOffItem::class);
    }
}
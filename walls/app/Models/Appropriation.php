<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appropriation extends Model
{
    protected $fillable = [
        'warehouse_id',
        'appropriation_date',
        'comment',
    ];

    protected $casts = [
        'appropriation_date' => 'date',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(AppropriationItem::class);
    }
}
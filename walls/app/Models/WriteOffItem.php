<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WriteOffItem extends Model
{
    protected $fillable = [
        'write_off_id',
        'variant_id',
        'batch_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function writeOff()
    {
        return $this->belongsTo(WriteOff::class);
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}


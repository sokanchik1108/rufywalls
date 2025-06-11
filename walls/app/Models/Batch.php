<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = ['variant_id', 'stock', 'batch_code'];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{

    use HasFactory;

    protected $fillable = ['name'];

    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'batch_warehouse')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}

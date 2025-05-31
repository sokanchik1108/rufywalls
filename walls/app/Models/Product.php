<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;


    protected $fillable = [
        'name', 'article', 'country', 'color', 'party', 'sticking', 'material',
        'purchase_price', 'sale_price', 'brand', 'quantity', 'category_id','description',
        // Убери room_id из fillable — связи через many-to-many!
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'country',
        'sticking',
        'material',
        'purchase_price',
        'sale_price',
        'brand',
        'category_id',
        'description',
        'detailed',
        // Убери room_id из fillable — связи через many-to-many!
    ];

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class);
    }

        public function companions()
    {
        return $this->belongsToMany(
            Product::class,
            'product_companions',
            'product_id',
            'companion_id'
        );
    }

    // Обратная связь — этот товар может быть компаньоном другого
    public function companionFor()
    {
        return $this->belongsToMany(
            Product::class,
            'product_companions',
            'companion_id',
            'product_id'
        );
    }

    public function companionOf()
{
 return $this->belongsToMany(Product::class, 'product_companions', 'product_id', 'companion_id');
}

}

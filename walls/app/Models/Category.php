<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['category_name', 'slug'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->slug = Str::slug($category->category_name, '-');
        });

        static::updating(function ($category) {
            // Если имя изменилось — обновляем slug
            if ($category->isDirty('category_name')) {
                $category->slug = Str::slug($category->category_name, '-');
            }
        });
    }

    // Категория имеет много обоев
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Category::create(['category_name' => 'Однотонные']);
        Category::create(['category_name' => 'Геометрические']);
        Category::create(['category_name' => 'С узорами']);
        Category::create(['category_name' => 'Цветочные']);
        Category::create(['category_name' => 'Абстрактные']);
        Category::create(['category_name' => 'Детские']);
        Category::create(['category_name' => 'Фактурные']);
        Category::create(['category_name' => 'Классические']);
        Category::create(['category_name' => 'Современные']);
        Category::create(['category_name' => 'С 3D эффектом']);
    }
}

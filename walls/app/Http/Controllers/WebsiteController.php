<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebsiteController extends Controller
{
public function website()
{
    $products = Product::with('category', 'rooms')->take(3)->get(); // только 3 товара

    $categories = Category::all();
    $rooms = Room::all();

    return view('website', compact('products', 'categories', 'rooms'));
}

}

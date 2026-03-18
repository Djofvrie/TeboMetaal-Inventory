<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CatalogController extends Controller
{
    public function index()
    {
        $categories = Category::with(['products.variants.stockItems'])
            ->orderBy('sort_order')
            ->get();

        return view('catalog', compact('categories'));
    }
}

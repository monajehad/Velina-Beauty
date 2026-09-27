<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Main storefront: shows all active products, optionally filtered by category.
     */
    public function index(Request $request)
    {
        $categories = Category::orderBy('sort_order')->get();

        $products = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
            })
            ->orderByDesc('id')
            ->get();

        $whatsappNumber = config('services.whatsapp.number');

        return view('catalog.index', compact('categories', 'products', 'whatsappNumber'));
    }
}

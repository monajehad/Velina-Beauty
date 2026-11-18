<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $whatsappNumber = config('services.whatsapp.number');

        return view('catalog.index', compact('categories', 'products', 'whatsappNumber'));
    }

    /**
     * Cart page. The cart itself lives in the browser (sessionStorage), so this
     * only renders the page shell; JavaScript fills in the items.
     */
    public function cart()
    {
        $whatsappNumber = config('services.whatsapp.number');

        return view('catalog.cart', compact('whatsappNumber'));
    }

    /**
     * Stores the order PDF made in the browser and returns its public link,
     * which is added to the WhatsApp message (WhatsApp links can't attach files).
     */
    public function storeOrderPdf(Request $request)
    {
        $data = $request->validate([
            'pdf' => ['required', 'file', 'mimetypes:application/pdf', 'max:8192'],
            'order_number' => ['required', 'string', 'regex:/^ORD-[0-9]{8}-[0-9]{4}-[0-9]{3}$/'],
        ]);

        // Random suffix so order links can't be guessed from the order number
        $name = $data['order_number'] . '-' . Str::random(12) . '.pdf';
        $path = $request->file('pdf')->storeAs('orders', $name, 'public');

        return response()->json(['url' => asset('storage/' . $path)]);
    }
}

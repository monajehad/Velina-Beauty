<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
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
     * Saves an order when the customer taps "Confirm order via WhatsApp",
     * so it shows up in the admin Orders page. Product details are taken
     * from the database, only ids and quantities come from the browser.
     */
    public function storeOrder(Request $request)
    {
        $data = $request->validate([
            'order_number' => ['required', 'string', 'regex:/^ORD-[0-9]{8}-[0-9]{4}-[0-9]{3}$/'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_address' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1', 'max:500'],
            'items.*.id' => ['required', 'integer'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        $products = Product::whereIn('id', collect($data['items'])->pluck('id'))->get()->keyBy('id');

        $items = collect($data['items'])
            ->filter(fn ($item) => $products->has($item['id']))
            ->map(fn ($item) => [
                'product_id' => $products[$item['id']]->id,
                'name' => $products[$item['id']]->name,
                'barcode' => $products[$item['id']]->barcode,
                'image' => $products[$item['id']]->thumbUrl(),
                'qty' => (int) $item['qty'],
            ])
            ->values();

        if ($items->isEmpty()) {
            return response()->json(['message' => 'No valid products'], 422);
        }

        Order::updateOrCreate(
            ['order_number' => $data['order_number']],
            [
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_address' => $data['customer_address'] ?? null,
                'items' => $items->all(),
                'total_pcs' => $items->sum('qty'),
                'locale' => app()->getLocale(),
            ]
        );

        return response()->json(['ok' => true]);
    }
}

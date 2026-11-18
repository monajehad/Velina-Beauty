<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Services\WhatsAppSender;
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

        $serverWhatsApp = WhatsAppSender::enabled();

        return view('catalog.cart', compact('whatsappNumber', 'serverWhatsApp'));
    }

    /**
     * Saves an order when the customer taps "Confirm order via WhatsApp",
     * so it shows up in the admin Orders page.
     */
    public function storeOrder(Request $request)
    {
        $order = $this->saveOrder($request);

        return $order
            ? response()->json(['ok' => true])
            : response()->json(['message' => 'No valid products'], 422);
    }

    /**
     * Server-side WhatsApp: saves the order with its PDF (made in the browser)
     * and the server sends that PDF to the store's WhatsApp number.
     * Only used when WHATSAPP_DRIVER is configured.
     */
    public function sendOrder(Request $request, WhatsAppSender $sender)
    {
        abort_unless(WhatsAppSender::enabled(), 404);

        $request->validate([
            'pdf' => ['required', 'file', 'mimetypes:application/pdf', 'max:8192'],
        ]);

        $order = $this->saveOrder($request);
        if (! $order) {
            return response()->json(['message' => 'No valid products'], 422);
        }

        // Random suffix so order links can't be guessed from the order number
        $path = $request->file('pdf')->storeAs('orders', $order->order_number . '-' . Str::random(12) . '.pdf', 'public');
        $order->update(['pdf_path' => $path]);

        $caption = trim(implode("\n", array_filter([
            'Order #: ' . $order->order_number,
            $order->customer_name ? 'Customer: ' . $order->customer_name : null,
            $order->customer_phone ? 'Phone: ' . $order->customer_phone : null,
            $order->customer_address ? 'Address: ' . $order->customer_address : null,
            'Total: ' . $order->total_pcs . ' pcs',
        ])));

        $sent = $sender->sendDocument(
            config('services.whatsapp.number'),
            asset('storage/' . $path),
            $order->order_number . '.pdf',
            $caption,
            $order->order_number,
        );

        if ($sent) {
            $order->update(['whatsapp_sent_at' => now()]);
        }

        return response()->json(['sent' => $sent]);
    }

    /**
     * Validates and stores an order. Product details come from the database;
     * only ids and quantities come from the browser. Returns null if no
     * valid products were sent.
     */
    private function saveOrder(Request $request): ?Order
    {
        // Multipart requests send the items list as a JSON string
        if (is_string($request->input('items'))) {
            $request->merge(['items' => json_decode($request->input('items'), true)]);
        }

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
            return null;
        }

        return Order::updateOrCreate(
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
    }
}

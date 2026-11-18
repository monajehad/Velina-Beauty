<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\ProductThumbnail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->q;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);
        ProductThumbnail::make($product);

        return redirect()->route('admin.products.index')->with('status', 'product_added');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('sort_order')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request, $product->id);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
                ProductThumbnail::delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        if ($request->hasFile('image')) {
            ProductThumbnail::make($product);
        }

        return redirect()->route('admin.products.index')->with('status', 'product_updated');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
            ProductThumbnail::delete($product->image_path);
        }
        $product->delete();

        return back()->with('status', 'product_deleted');
    }

    /**
     * Bulk-toggle active/inactive for quick catalog cleanup.
     */
    public function toggleActive(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('status', 'product_status_updated');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $inputKeys = array_keys($request->all());
        $isPartial = $request->has('sort_order') && count($inputKeys) <= 4; // sort_order + _token + _method + maybe category_id

        // For partial updates, only validate the fields that are actually present
        $rules = [];

        // Always validate sort_order if present
        if ($request->has('sort_order')) {
            $rules['sort_order'] = ['nullable', 'integer', 'min:0'];
        }

        // For full updates (create/edit form), validate all required fields
        if (!$isPartial) {
            $rules = array_merge($rules, [
                'category_id' => ['required', 'exists:categories,id'],
                'name' => ['required', 'string', 'max:255'],
                'barcode' => ['required', 'string', 'max:64'],
                'price' => ['required', 'numeric', 'min:0'],
                'default_qty' => ['required', 'integer', 'min:1'],
                'is_active' => ['sometimes', 'boolean'],
                'image' => ['nullable', 'image', 'max:4096'],
            ]);
        }

        return $request->validate($rules);
    }
}

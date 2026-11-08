@csrf
<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="text-xs text-gray-600">{{ __('product_name') }}</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
               class="w-full border rounded-lg px-3 py-2 text-sm mt-1">
    </div>

    <div>
        <label class="text-xs text-gray-600">{{ __('category') }}</label>
        <select name="category_id" required class="w-full border rounded-lg px-3 py-2 text-sm mt-1">
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? null) == $cat->id)>
                    {{ $cat->icon }} {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="text-xs text-gray-600">
            {{ __('barcode_number') }}
            <span class="text-gray-400">({{ __('barcode_hint') }})</span>
        </label>
        <input type="text" name="barcode" id="barcode-input" dir="ltr" value="{{ old('barcode', $product->barcode ?? '') }}" required
               class="w-full border rounded-lg px-3 py-2 text-sm mt-1 font-mono">
        <svg id="barcode-preview" class="mt-2"></svg>
    </div>

    <div>
        <label class="text-xs text-gray-600">{{ __('price_omr') }}</label>
        <input type="number" step="0.001" min="0" name="price" value="{{ old('price', $product->price ?? '') }}" required
               class="w-full border rounded-lg px-3 py-2 text-sm mt-1">
    </div>

    <div>
        <label class="text-xs text-gray-600">{{ __('default_quantity') }}</label>
        <input type="number" min="1" name="default_qty" value="{{ old('default_qty', $product->default_qty ?? 50) }}" required
               class="w-full border rounded-lg px-3 py-2 text-sm mt-1">
    </div>

    <div>
        <label class="text-xs text-gray-600">{{ __('display_order') }}</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}"
               class="w-full border rounded-lg px-3 py-2 text-sm mt-1">
        <span class="text-xs text-gray-400 mt-1 block">{{ __('sort_hint') }}</span>
    </div>

    <div>
        <label class="text-xs text-gray-600">{{ __('product_image') }}</label>
        <input type="file" name="image" accept="image/*" class="w-full border rounded-lg px-3 py-2 text-sm mt-1 bg-white">
        @if (! empty($product) && $product->image_path)
            <img src="{{ $product->imageUrl() }}" class="w-16 h-16 rounded object-cover mt-2">
        @endif
    </div>

    <label class="flex items-center gap-2 text-sm mt-2">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))>
        {{ __('visible_in_storefront') }}
    </label>
</div>

<button type="submit" class="mt-5 bg-maroon text-white text-sm rounded-lg px-5 py-2">{{ __('save_product') }}</button>

<script src="https://cdnjs.cloudflare.com/ajax/libs/JsBarcode/3.11.5/JsBarcode.all.min.js"></script>
<script>
    function renderBarcodePreview() {
        const value = document.getElementById('barcode-input').value.trim();
        const svg = document.getElementById('barcode-preview');
        if (! value) { svg.innerHTML = ''; return; }
        try {
            JsBarcode(svg, value, { format: 'CODE128', width: 1.4, height: 40, fontSize: 12 });
        } catch (e) { svg.innerHTML = ''; }
    }
    document.getElementById('barcode-input').addEventListener('input', renderBarcodePreview);
    renderBarcodePreview();
</script>

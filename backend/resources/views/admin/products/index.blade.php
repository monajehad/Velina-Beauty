@extends('layouts.admin')
@section('title', __('products'))

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-lg font-semibold text-maroon">{{ __('products') }}</h1>
    <a href="{{ route('admin.products.create') }}" class="bg-maroon text-white text-sm rounded-lg px-4 py-2">+ {{ __('add_product') }}</a>
</div>

<form method="GET" class="mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('search_placeholder') }}"
           class="w-full md:w-80 border rounded-lg px-3 py-2 text-sm">
</form>

<div class="bg-white rounded-xl overflow-x-auto">
    <table class="w-full text-sm min-w-[640px]">
        <thead>
            <tr class="text-xs text-gray-500 border-b">
                <th class="p-3 text-start">{{ __('image') }}</th>
                <th class="text-start">{{ __('name') }}</th>
                <th class="text-start">{{ __('category') }}</th>
                <th class="text-start">{{ __('barcode') }}</th>
                <th class="text-start">{{ __('price') }}</th>
                <th class="text-start">{{ __('default_qty') }}</th>
                <th class="text-start">{{ __('sort_order') }}</th>
                <th class="text-start">{{ __('status') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr class="border-b last:border-0">
                    <td class="p-3">
                        <img src="{{ $product->imageUrl() }}" class="w-10 h-10 rounded object-cover">
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td dir="ltr" class="text-start">{{ $product->barcode }}</td>
                    <td>{{ number_format($product->price, 3) }} {{ __('price_prefix') }}</td>
                    <td>{{ $product->default_qty }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.products.update', $product) }}" class="inline-flex items-center gap-1">
                            @csrf @method('PUT')
                            <input type="number" name="sort_order" min="0" value="{{ $product->sort_order }}" class="w-16 border rounded px-2 py-1 text-xs text-center">
                            <button type="submit" class="text-xs text-maroon hover:underline">{{ __('save') }}</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.products.toggle', $product) }}">
                            @csrf @method('PATCH')
                            <button class="text-xs rounded-full px-2 py-1 {{ $product->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $product->is_active ? __('active_status') : __('hidden_status') }}
                            </button>
                        </form>
                    </td>
                    <td class="p-3 whitespace-nowrap">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-xs text-maroon underline me-2">{{ __('edit') }}</a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline"
                              onsubmit="return confirm(@js(__('confirm_delete')));">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 underline">{{ __('delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="p-6 text-center text-gray-500">{{ __('no_products_yet') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $products->links() }}</div>
@endsection

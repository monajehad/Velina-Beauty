@extends('layouts.admin')
@section('title', 'Products')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-lg font-semibold text-maroon">Products</h1>
    <a href="{{ route('admin.products.create') }}" class="bg-maroon text-white text-sm rounded-lg px-4 py-2">+ Add product</a>
</div>

<form method="GET" class="mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name or barcode..."
           class="w-full md:w-80 border rounded-lg px-3 py-2 text-sm">
</form>

<div class="bg-white rounded-xl overflow-x-auto">
    <table class="w-full text-sm min-w-[640px]">
        <thead>
            <tr class="text-left text-xs text-gray-500 border-b">
                <th class="p-3">Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Barcode</th>
                <th>Price</th>
                <th>Default qty</th>
                <th>Status</th>
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
                    <td>#{{ $product->barcode }}</td>
                    <td>{{ number_format($product->price, 3) }} {{ __('price_prefix') }}</td>
                    <td>{{ $product->default_qty }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.products.toggle', $product) }}">
                            @csrf @method('PATCH')
                            <button class="text-xs rounded-full px-2 py-1 {{ $product->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $product->is_active ? 'Active' : 'Hidden' }}
                            </button>
                        </form>
                    </td>
                    <td class="p-3 space-x-2 whitespace-nowrap">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-xs text-maroon underline">Edit</a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline"
                              onsubmit="return confirm('Delete this product?');">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-600 underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="p-6 text-center text-gray-500">No products yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $products->links() }}</div>
@endsection

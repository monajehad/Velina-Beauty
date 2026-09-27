@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<h1 class="text-lg font-semibold text-maroon mb-4">Dashboard</h1>

<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded-xl p-4">
        <div class="text-xs text-gray-500">Total products</div>
        <div class="text-2xl font-semibold text-maroon">{{ $stats['products'] }}</div>
    </div>
    <div class="bg-white rounded-xl p-4">
        <div class="text-xs text-gray-500">Categories</div>
        <div class="text-2xl font-semibold text-maroon">{{ $stats['categories'] }}</div>
    </div>
    <div class="bg-white rounded-xl p-4">
        <div class="text-xs text-gray-500">Active</div>
        <div class="text-2xl font-semibold text-green-600">{{ $stats['active'] }}</div>
    </div>
    <div class="bg-white rounded-xl p-4">
        <div class="text-xs text-gray-500">Inactive</div>
        <div class="text-2xl font-semibold text-gray-400">{{ $stats['inactive'] }}</div>
    </div>
</div>

<div class="bg-white rounded-xl p-4">
    <div class="text-sm font-medium mb-3">Recently added products</div>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-xs text-gray-500 border-b">
                <th class="py-2">Product</th>
                <th>Category</th>
                <th>Barcode</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($recentProducts as $p)
                <tr class="border-b last:border-0">
                    <td class="py-2">{{ $p->name }}</td>
                    <td>{{ $p->category->name }}</td>
                    <td>#{{ $p->barcode }}</td>
                    <td>{{ number_format($p->price, 3) }} {{ __('price_prefix') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<a href="{{ route('admin.products.create') }}" class="inline-block mt-4 bg-maroon text-white text-sm rounded-lg px-4 py-2">
    + Add new product
</a>
@endsection

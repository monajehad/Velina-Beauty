@extends('layouts.admin')
@section('title', 'Categories')

@section('content')
<h1 class="text-lg font-semibold text-maroon mb-4">Categories</h1>

<div class="bg-white rounded-xl p-4 mb-6 max-w-md">
    <div class="text-sm font-medium mb-3">Add a new category</div>
    <form method="POST" action="{{ route('admin.categories.store') }}" class="flex gap-2">
        @csrf
        <input type="text" name="icon" placeholder="🎀" maxlength="4" class="w-14 border rounded-lg px-2 py-2 text-sm text-center">
        <input type="text" name="name" placeholder="Category name" required class="flex-1 border rounded-lg px-3 py-2 text-sm">
        <button class="bg-maroon text-white text-sm rounded-lg px-4">Add</button>
    </form>
</div>

<div class="bg-white rounded-xl overflow-x-auto max-w-2xl">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-xs text-gray-500 border-b">
                <th class="p-3">Icon</th>
                <th>Name</th>
                <th>Products</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $cat)
                <tr class="border-b last:border-0">
                    <form method="POST" action="{{ route('admin.categories.update', $cat) }}">
                        @csrf @method('PUT')
                        <td class="p-3"><input type="text" name="icon" value="{{ $cat->icon }}" class="w-12 border rounded px-1 py-1 text-center"></td>
                        <td><input type="text" name="name" value="{{ $cat->name }}" class="border rounded px-2 py-1"></td>
                        <td>{{ $cat->products_count }}</td>
                        <td class="p-3 space-x-2 whitespace-nowrap">
                            <button class="text-xs text-maroon underline">Save</button>
                    </form>
                            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" class="inline"
                                  onsubmit="return confirm('Delete this category? Only possible if it has no products.');">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-600 underline">Delete</button>
                            </form>
                        </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

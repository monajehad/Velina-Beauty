@extends('layouts.admin')
@section('title', 'Edit Product')

@section('content')
<h1 class="text-lg font-semibold text-maroon mb-4">Edit product</h1>

<div class="bg-white rounded-xl p-5 max-w-2xl">
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.products._form')
    </form>
</div>
@endsection

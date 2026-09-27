@extends('layouts.admin')
@section('title', 'Add Product')

@section('content')
<h1 class="text-lg font-semibold text-maroon mb-4">Add product</h1>

<div class="bg-white rounded-xl p-5 max-w-2xl">
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @include('admin.products._form')
    </form>
</div>
@endsection

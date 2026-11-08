@extends('layouts.admin')
@section('title', __('edit_product'))

@section('content')
<h1 class="text-lg font-semibold text-maroon mb-4">{{ __('edit_product') }}</h1>

<div class="bg-white rounded-xl p-5 max-w-2xl">
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.products._form')
    </form>
</div>
@endsection

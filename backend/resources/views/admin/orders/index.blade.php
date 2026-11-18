@extends('layouts.admin')
@section('title', __('orders'))

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-lg font-semibold text-maroon">{{ __('orders') }}</h1>
</div>

<form method="GET" class="mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('search_orders_placeholder') }}"
           class="w-full md:w-80 border rounded-lg px-3 py-2 text-sm">
</form>

<div class="bg-white rounded-xl overflow-x-auto">
    <table class="w-full text-sm min-w-[720px]">
        <thead>
            <tr class="text-xs text-gray-500 border-b">
                <th class="p-3 text-start">{{ __('order_number') }}</th>
                <th class="px-3 text-start">{{ __('date') }}</th>
                <th class="px-3 text-start">{{ __('customer_name') }}</th>
                <th class="px-3 text-start">{{ __('customer_phone') }}</th>
                <th class="px-3 text-start">{{ __('total') }}</th>
                <th class="px-3 text-start">PDF</th>
                <th class="px-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr class="border-b last:border-0 hover:bg-cream/50">
                    <td class="px-3 py-3 font-mono font-semibold text-maroon whitespace-nowrap">
                        <a href="{{ route('admin.orders.show', $order) }}" class="hover:underline" dir="ltr">{{ $order->order_number }}</a>
                    </td>
                    <td class="px-3 whitespace-nowrap"><span dir="ltr">{{ $order->created_at->format('Y-m-d H:i') }}</span></td>
                    <td class="px-3">{{ $order->customer_name ?: '—' }}</td>
                    <td class="px-3 whitespace-nowrap"><span dir="ltr">{{ $order->customer_phone ?: '—' }}</span></td>
                    <td class="px-3 whitespace-nowrap">{{ $order->total_pcs }} {{ __('pcs') }}</td>
                    <td class="px-3">
                        <a href="{{ route('admin.orders.show', [$order, 'pdf' => 1]) }}"
                           class="inline-flex items-center gap-1 text-xs bg-maroon text-white rounded-lg px-3 py-1.5 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16"/></svg>
                            {{ __('download_pdf') }}
                        </a>
                    </td>
                    <td class="p-3 whitespace-nowrap">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-maroon underline me-2">{{ __('view') }}</a>
                        <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="inline"
                              onsubmit="return confirm(@js(__('confirm_delete_order')));">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 underline">{{ __('delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="p-6 text-center text-gray-500">{{ __('no_orders_yet') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $orders->links() }}</div>
@endsection

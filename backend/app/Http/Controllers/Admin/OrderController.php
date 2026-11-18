<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%' . $request->input('q') . '%';
                $query->where(fn ($w) => $w->where('order_number', 'like', $q)
                    ->orWhere('customer_name', 'like', $q)
                    ->orWhere('customer_phone', 'like', $q));
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /** Order sheet; "?pdf=1" downloads it as a PDF right away. */
    public function show(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('status', 'order_deleted');
    }
}

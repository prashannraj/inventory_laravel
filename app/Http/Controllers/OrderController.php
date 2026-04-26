<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;

use App\Models\Sale;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sale::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('invoice_no', 'like', '%' . $request->search . '%')
                  ->orWhereHas('customer', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        $orders = $query->latest()->paginate(15);
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('sales.create');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Sale::with(['customer', 'items.product', 'payments', 'store'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $order = Sale::findOrFail($id);
        
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated successfully.');
    }
}

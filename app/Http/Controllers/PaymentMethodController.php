<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::all();
        return view('payment-methods.index', compact('methods'));
    }

    public function create()
    {
        return view('payment-methods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:offline,online',
            'gateway' => 'nullable|in:esewa,khalti',
            'details' => 'nullable|string',
            'active' => 'boolean',
        ]);

        // If gateway is set, ensure type is online
        $data = $request->all();
        if (!empty($data['gateway'])) {
            $data['type'] = 'online';
        }

        // Handle checkbox: if active is not present in request, set it to false
        if (!isset($data['active'])) {
            $data['active'] = false;
        }

        PaymentMethod::create($data);

        return redirect()->route('payment-methods.index')->with('success', 'Payment method created successfully.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:offline,online',
            'gateway' => 'nullable|in:esewa,khalti',
            'details' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $data = $request->all();
        if (!empty($data['gateway'])) {
            $data['type'] = 'online';
        }

        // Handle checkbox: if active is not present in request, set it to false
        if (!isset($data['active'])) {
            $data['active'] = false;
        }

        $paymentMethod->update($data);

        return redirect()->route('payment-methods.index')->with('success', 'Payment method updated successfully.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return redirect()->route('payment-methods.index')->with('success', 'Payment method deleted successfully.');
    }
}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Order Details') }}: {{ $order->invoice_no }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('orders.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
                <a href="{{ route('sales.invoice', $order) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                    <i class="fas fa-print mr-1"></i> Print Invoice
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Order Information -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Items Ordered</h3>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                                    <th class="text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                                    <th class="text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="py-3 text-sm">{{ $item->product->name }}</td>
                                    <td class="py-3 text-sm text-center">{{ $item->quantity }}</td>
                                    <td class="py-3 text-sm text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="py-3 text-sm text-right font-bold">Rs. {{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="pt-4 text-right text-sm text-gray-500">Gross Total:</td>
                                    <td class="pt-4 text-right text-sm font-bold">Rs. {{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right text-sm text-gray-500">Discount:</td>
                                    <td class="text-right text-sm text-red-600">- Rs. {{ number_format($order->discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right text-lg font-bold">Net Total:</td>
                                    <td class="text-right text-lg font-bold text-indigo-600">Rs. {{ number_format($order->net_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Payment History</h3>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                    <th class="text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($order->payments as $payment)
                                <tr>
                                    <td class="py-3 text-sm">{{ $payment->date->format('Y-m-d') }}</td>
                                    <td class="py-3 text-sm">{{ ucfirst($payment->payment_method) }}</td>
                                    <td class="py-3 text-sm text-right font-bold text-green-600">Rs. {{ number_format($payment->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Order Status & Customer -->
                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Update Status</h3>
                        <form action="{{ route('orders.update', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <x-input-label for="status" :value="__('Current Status')" />
                                <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <x-primary-button class="w-full justify-center">
                                Update Status
                            </x-primary-button>
                        </form>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Customer Info</h3>
                        <div class="space-y-2 text-sm">
                            <p><span class="text-gray-500">Name:</span> {{ $order->customer?->name ?? 'Walk-in Customer' }}</p>
                            <p><span class="text-gray-500">Phone:</span> {{ $order->customer?->phone ?? 'N/A' }}</p>
                            <p><span class="text-gray-500">Email:</span> {{ $order->customer?->email ?? 'N/A' }}</p>
                            <p><span class="text-gray-500">Address:</span> {{ $order->customer?->address ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Other Details</h3>
                        <div class="space-y-2 text-sm">
                            <p><span class="text-gray-500">Store:</span> {{ $order->store?->name }}</p>
                            <p><span class="text-gray-500">Created By:</span> {{ $order->user?->name }}</p>
                            <p><span class="text-gray-500">Notes:</span> {{ $order->notes ?? 'No notes' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

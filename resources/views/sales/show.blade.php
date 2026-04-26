<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sale Details') }}: {{ $sale->invoice_no }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('sales.invoice', $sale) }}" target="_blank" class="bg-emerald-500 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded text-sm flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i> Print Invoice
                </a>
                <a href="{{ route('sales.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <!-- Invoice Header Section -->
                    <div class="flex flex-col md:flex-row justify-between border-b pb-8 mb-8 gap-8">
                        <div>
                            <h3 class="text-3xl font-black text-indigo-600 uppercase tracking-tighter mb-4">Invoice</h3>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">Customer Entity</p>
                                <p class="text-lg font-black text-gray-900 uppercase tracking-tighter">{{ $sale->customer?->name ?? 'Walk-in Guest' }}</p>
                                @if($sale->customer)
                                    <p class="text-sm text-gray-600 font-medium italic">{{ $sale->customer->email }}</p>
                                    <p class="text-sm text-gray-600 font-medium italic">{{ $sale->customer->phone }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-left md:text-right space-y-4">
                            <div>
                                <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">Transaction Date</p>
                                <p class="text-lg font-black text-gray-900 uppercase tracking-tighter">{{ $sale->date->format('F d, Y - H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">Payment Status</p>
                                @php
                                    $statusColors = [
                                        'paid' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'partial' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'unpaid' => 'bg-rose-100 text-rose-700 border-rose-200',
                                    ];
                                @endphp
                                <span class="px-4 py-1.5 inline-flex text-xs font-black uppercase tracking-widest rounded-full border {{ $statusColors[$sale->payment_status] }}">
                                    {{ $sale->payment_status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <h4 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Line Items</h4>
                    <div class="overflow-x-auto mb-10 border border-gray-100 rounded-2xl">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    <th class="px-6 py-4">Product Specification</th>
                                    <th class="px-6 py-4 text-center">Qty</th>
                                    <th class="px-6 py-4 text-right">Unit Price</th>
                                    <th class="px-6 py-4 text-right">Discount</th>
                                    <th class="px-6 py-4 text-right">Tax Rate</th>
                                    <th class="px-6 py-4 text-right">Tax Amount</th>
                                    <th class="px-6 py-4 text-right">Extended Price</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($sale->items as $item)
                                <tr class="hover:bg-indigo-50/10 transition-colors">
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-gray-900 uppercase tracking-tighter">{{ $item->product->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">SKU: {{ $item->product->sku }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-center font-bold text-gray-700">{{ $item->quantity }}</td>
                                    <td class="px-6 py-5 text-right font-bold text-gray-900">Rs. {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-6 py-5 text-right font-bold text-rose-500">- Rs. {{ number_format($item->discount, 2) }}</td>
                                    <td class="px-6 py-5 text-right font-bold text-gray-700">
                                        @if($item->product->taxRate)
                                            {{ $item->product->taxRate->name }} ({{ number_format($item->product->taxRate->rate, 2) }}%)
                                        @else
                                            <span class="text-gray-400">No Tax</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-right font-bold text-emerald-500">+ Rs. {{ number_format($item->tax_amount, 2) }}</td>
                                    <td class="px-6 py-5 text-right font-black text-indigo-600">Rs. {{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary & Footer -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                        <div class="lg:col-span-2 space-y-8">
                            <div>
                                <h4 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Transaction Intelligence</h4>
                                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 italic text-gray-600 text-sm leading-relaxed">
                                    {{ $sale->notes ?: 'No additional metadata or observations recorded for this transaction.' }}
                                </div>
                            </div>

                            <div>
                                <h4 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Payment Log</h4>
                                <div class="space-y-3">
                                    @forelse($sale->payments as $payment)
                                    <div class="flex items-center justify-between p-4 bg-white border border-gray-100 rounded-xl shadow-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-black text-gray-900 uppercase tracking-tighter">Settlement Recieved</p>
                                                <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $payment->date->format('M d, Y') }} | {{ strtoupper($payment->payment_method) }}</p>
                                            </div>
                                        </div>
                                        <p class="text-sm font-black text-emerald-600 tracking-tight">Rs. {{ number_format($payment->amount, 2) }}</p>
                                    </div>
                                    @empty
                                    <div class="text-center py-6 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest italic">No payment cycles recorded</p>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-900 p-8 rounded-3xl text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
                                <i class="fas fa-file-invoice text-9xl"></i>
                            </div>
                            
                            <h4 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.3em] mb-8">Financial Recapitulation</h4>
                            
                            <div class="space-y-6 relative z-10">
                                <div class="flex justify-between items-center border-b border-white/10 pb-4">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Gross Total</span>
                                    <span class="text-lg font-black tracking-tight">Rs. {{ number_format($sale->total_amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-white/10 pb-4">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest text-rose-400">Total Rebate</span>
                                    <span class="text-lg font-black tracking-tight text-rose-400">- Rs. {{ number_format($sale->discount, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-white/10 pb-4">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest text-emerald-400">Tax Levied</span>
                                    <span class="text-lg font-black tracking-tight text-emerald-400">+ Rs. {{ number_format($sale->tax_amount, 2) }}</span>
                                </div>
                                <div class="pt-4">
                                    <span class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.3em]">Payable Net Amount</span>
                                    <div class="text-4xl font-black tracking-tighter mt-2">Rs. {{ number_format($sale->net_amount, 2) }}</div>
                                </div>

                                <div class="mt-10 pt-10 border-t border-white/10">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Amount Disbursed</span>
                                        <span class="text-sm font-black text-emerald-400 tracking-tight">Rs. {{ number_format($sale->payments->sum('amount'), 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest text-rose-400">Outstanding Debt</span>
                                        <span class="text-sm font-black text-rose-400 tracking-tight">Rs. {{ number_format($sale->net_amount - $sale->payments->sum('amount'), 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

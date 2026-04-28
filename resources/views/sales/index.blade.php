<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sales') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium">Sales History</h3>
                        <div class="space-x-2">
                            <a href="{{ route('sales.create') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-plus mr-1"></i> Manual Sale
                            </a>
                            <a href="{{ route('sales.pos') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-cash-register mr-1"></i> POS Interface
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto -mx-2 xs:mx-0">
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="py-3 pl-4 pr-3 text-left text-xs font-semibold text-gray-900 sm:pl-6">Date</th>
                                            <th scope="col" class="hidden sm:table-cell px-3 py-3 text-left text-xs font-semibold text-gray-900">Invoice No</th>
                                            <th scope="col" class="hidden md:table-cell px-3 py-3 text-left text-xs font-semibold text-gray-900">Customer</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-900">Status</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-900">Total</th>
                                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-900">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach($sales as $sale)
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                                <div class="font-medium text-gray-900">{{ $sale->date->format('Y-m-d') }}</div>
                                                <div class="text-gray-500 text-xs">{{ $sale->date->format('H:i') }}</div>
                                            </td>
                                            <td class="hidden sm:table-cell whitespace-nowrap px-3 py-4 text-sm text-gray-900 font-medium">
                                                {{ $sale->invoice_no }}
                                            </td>
                                            <td class="hidden md:table-cell whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ $sale->customer?->name ?? 'Guest Customer' }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                @php
                                                    $statusColors = [
                                                        'paid' => 'bg-green-100 text-green-800',
                                                        'partial' => 'bg-yellow-100 text-yellow-800',
                                                        'pending' => 'bg-blue-100 text-blue-800',
                                                        'unpaid' => 'bg-red-100 text-red-800',
                                                    ];
                                                @endphp
                                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $statusColors[$sale->payment_status] }}">
                                                    {{ ucfirst($sale->payment_status) }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm font-bold text-gray-900">
                                                Rs. {{ number_format($sale->net_amount, 2) }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm font-medium">
                                                <div class="flex items-center space-x-3">
                                                    <a href="{{ route('sales.show', $sale) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('sales.invoice', $sale) }}" target="_blank" class="text-green-600 hover:text-green-900" title="Download Invoice">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        {{ $sales->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

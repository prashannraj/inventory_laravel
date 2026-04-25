<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Supplier Details') }}: {{ $supplier->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('suppliers.edit', $supplier) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Edit Supplier
                </a>
                <a href="{{ route('suppliers.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-lg font-medium border-b pb-2 mb-4">Contact Information</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Email:</span>
                                    <span class="font-medium">{{ $supplier->email ?: 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Phone:</span>
                                    <span class="font-medium">{{ $supplier->phone ?: 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Tax Number:</span>
                                    <span class="font-medium">{{ $supplier->tax_number ?: 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Address:</span>
                                    <span class="font-medium block mt-1 whitespace-pre-line">{{ $supplier->address ?: 'No address provided.' }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium border-b pb-2 mb-4">Financial & Status</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Opening Balance:</span>
                                    <span class="font-medium font-mono">Rs. {{ number_format($supplier->opening_balance, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Status:</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $supplier->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $supplier->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12">
                        <h3 class="text-lg font-medium border-b pb-2 mb-4">Recent Purchases</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase #</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($supplier->purchases()->latest()->take(5)->get() as $purchase)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $purchase->created_at->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">#{{ $purchase->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ ucfirst($purchase->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">Rs. {{ number_format($purchase->grand_total, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No purchases found for this supplier.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

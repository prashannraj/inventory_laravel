<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-badge {
            font-size: 0.85rem;
            padding: 5px 12px;
        }
        .gateway-badge {
            font-size: 0.85rem;
            padding: 5px 12px;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,0.03);
        }
        .amount-cell {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h3 class="mb-0"><i class="bi bi-clock-history"></i> Payment History</h3>
                        <a href="{{ route('payment.index') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-credit-card"></i> New Payment
                        </a>
                    </div>
                    <div class="card-body">
                        
                        <!-- Filters -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <form method="GET" class="row g-2">
                                    <div class="col-md-3">
                                        <select name="status" class="form-select">
                                            <option value="">All Status</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                            <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="gateway" class="form-select">
                                            <option value="">All Gateways</option>
                                            <option value="esewa" {{ request('gateway') == 'esewa' ? 'selected' : '' }}>eSewa</option>
                                            <option value="khalti" {{ request('gateway') == 'khalti' ? 'selected' : '' }}>Khalti</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-filter"></i> Filter
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="{{ route('payments.history') }}" class="btn btn-outline-secondary w-100">
                                            <i class="bi bi-arrow-clockwise"></i> Reset
                                        </a>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                <p class="mb-0">Total Records: <strong>{{ $payments->total() }}</strong></p>
                            </div>
                        </div>
                        
                        <!-- Payment Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Transaction ID</th>
                                        <th>Gateway</th>
                                        <th>Amount</th>
                                        <th>Product</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration + ($payments->currentPage() - 1) * $payments->perPage() }}</td>
                                        <td>
                                            <code>{{ $payment->transaction_id }}</code>
                                            @if($payment->pidx)
                                                <br><small class="text-muted">PIDX: {{ $payment->pidx }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($payment->payment_gateway == 'esewa')
                                                <span class="badge gateway-badge bg-success">eSewa</span>
                                            @else
                                                <span class="badge gateway-badge bg-purple" style="background-color: #5c2d91;">Khalti</span>
                                            @endif
                                        </td>
                                        <td class="amount-cell">NPR {{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->product_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($payment->status == 'completed')
                                                <span class="badge status-badge bg-success">Completed</span>
                                            @elseif($payment->status == 'pending')
                                                <span class="badge status-badge bg-warning text-dark">Pending</span>
                                            @elseif($payment->status == 'failed')
                                                <span class="badge status-badge bg-danger">Failed</span>
                                            @elseif($payment->status == 'refunded')
                                                <span class="badge status-badge bg-info">Refunded</span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="collapse" data-bs-target="#details{{ $payment->id }}">
                                                <i class="bi bi-eye"></i> Details
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="collapse" id="details{{ $payment->id }}">
                                        <td colspan="8" class="bg-light">
                                            <div class="p-3">
                                                <h6>Payment Details</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>User:</strong> {{ $payment->user->name ?? 'N/A' }}</p>
                                                        <p><strong>Order ID:</strong> {{ $payment->order_id ?? 'N/A' }}</p>
                                                        <p><strong>Refund ID:</strong> {{ $payment->refund_id ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Updated:</strong> {{ $payment->updated_at->format('Y-m-d H:i:s') }}</p>
                                                        <p><strong>Response:</strong> 
                                                            @if($payment->gateway_response)
                                                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#responseModal{{ $payment->id }}">
                                                                    View
                                                                </button>
                                                            @else
                                                                N/A
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Modal for Gateway Response -->
                                    @if($payment->gateway_response)
                                    <div class="modal fade" id="responseModal{{ $payment->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Gateway Response</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <pre class="bg-dark text-light p-3 rounded">{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="bi bi-receipt" style="font-size: 48px;"></i>
                                                <h5 class="mt-3">No payments found</h5>
                                                <p>You haven't made any payments yet.</p>
                                                <a href="{{ route('payment.index') }}" class="btn btn-primary">Make a Payment</a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($payments->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $payments->links() }}
                        </div>
                        @endif
                        
                    </div>
                </div>
                
                <div class="mt-4 text-center text-muted">
                    <small>Payment history is stored securely. You can view up to 6 months of transaction history.</small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>
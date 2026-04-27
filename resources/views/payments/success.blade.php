<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .success-icon {
            font-size: 80px;
            color: #28a745;
        }
        .receipt-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 25px;
            background: #f9f9f9;
        }
        .badge-status {
            font-size: 1rem;
            padding: 8px 15px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0"><i class="bi bi-check-circle"></i> Payment Successful</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="success-icon mb-4">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h2 class="text-success">Thank You!</h2>
                        <p class="lead">Your payment has been processed successfully.</p>
                        
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if(isset($payment) && $payment)
                        <div class="receipt-card mt-4 text-start">
                            <h4 class="border-bottom pb-2">Payment Receipt</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</p>
                                    <p><strong>Payment Gateway:</strong> 
                                        <span class="badge bg-primary">{{ ucfirst($payment->payment_gateway) }}</span>
                                    </p>
                                    <p><strong>Amount:</strong> NPR {{ number_format($payment->amount, 2) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> 
                                        <span class="badge badge-status bg-success">{{ ucfirst($payment->status) }}</span>
                                    </p>
                                    <p><strong>Product:</strong> {{ $payment->product_name }}</p>
                                    <p><strong>Date:</strong> {{ $payment->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p><strong>Note:</strong> This receipt is generated automatically. You can view it in your payment history.</p>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-info">
                            <p>Payment details are not available. You can check your payment history for details.</p>
                        </div>
                        @endif
                        
                        <div class="mt-5">
                            <a href="{{ route('payments.history') }}" class="btn btn-primary">
                                <i class="bi bi-clock-history"></i> View Payment History
                            </a>
                            <a href="{{ route('payment.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-credit-card"></i> Make Another Payment
                            </a>
                            <a href="/" class="btn btn-link">Back to Home</a>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-center text-muted">
                    <small>If you have any questions about this payment, please contact our support team.</small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>
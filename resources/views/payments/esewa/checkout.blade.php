<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eSewa Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .esewa-brand {
            color: #00a651;
            font-weight: bold;
        }
        .form-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 30px;
            background: #fff;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0"><i class="bi bi-credit-card"></i> Pay with <span class="esewa-brand">eSewa</span></h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> You will be redirected to eSewa's secure payment page after submitting this form.
                        </div>
                        
                        <form method="POST" action="{{ route('esewa.pay') }}">
                            @csrf
                            
                            <div class="form-card mt-4">
                                <div class="mb-3">
                                    <label for="product_name" class="form-label">Product/Service Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="product_name" name="product_name" 
                                           value="{{ old('product_name', 'Product Purchase') }}" required>
                                    @error('product_name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount (NPR) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">NPR</span>
                                        <input type="number" class="form-control" id="amount" name="amount" 
                                               min="1" step="0.01" value="{{ old('amount', 100) }}" required>
                                    </div>
                                    <div class="form-text">Minimum amount is NPR 1.</div>
                                    @error('amount')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Tax & Charges</label>
                                    <div class="border rounded p-3 bg-light">
                                        <div class="d-flex justify-content-between">
                                            <span>Tax Amount:</span>
                                            <span>NPR 0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Service Charge:</span>
                                            <span>NPR 0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Delivery Charge:</span>
                                            <span>NPR 0.00</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total Amount:</span>
                                            <span id="totalAmount">NPR 100.00</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <h6><i class="bi bi-shield-check"></i> Secure Payment</h6>
                                    <p class="mb-0">Your payment details are protected with SSL encryption. eSewa does not share your financial information.</p>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-lock"></i> Proceed to eSewa Payment
                                </button>
                                <a href="{{ route('payment.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to Gateway Selection
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="card">
                        <div class="card-body">
                            <h6><i class="bi bi-question-circle"></i> How to pay with eSewa?</h6>
                            <ol class="mb-0">
                                <li>Enter product name and amount</li>
                                <li>Click "Proceed to eSewa Payment"</li>
                                <li>You will be redirected to eSewa</li>
                                <li>Login with your eSewa ID and password</li>
                                <li>Confirm payment and enter OTP</li>
                                <li>You will be redirected back after success</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script>
        // Update total amount when amount changes
        document.getElementById('amount').addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            const tax = 0;
            const service = 0;
            const delivery = 0;
            const total = amount + tax + service + delivery;
            document.getElementById('totalAmount').textContent = 'NPR ' + total.toFixed(2);
        });
    </script>
</body>
</html>
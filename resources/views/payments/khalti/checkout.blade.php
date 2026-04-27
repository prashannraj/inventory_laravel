<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khalti Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .khalti-brand {
            color: #5c2d91;
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
                    <div class="card-header text-white" style="background-color: #5c2d91;">
                        <h3 class="mb-0"><i class="bi bi-wallet"></i> Pay with <span class="khalti-brand">Khalti</span></h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> You will be redirected to Khalti's secure payment page after submitting this form.
                        </div>
                        
                        <form method="POST" action="{{ route('khalti.pay') }}">
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
                                    <label class="form-label">Amount in Paisa</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Paisa</span>
                                        <input type="text" class="form-control" id="amount_paisa" readonly value="10000">
                                    </div>
                                    <div class="form-text">Khalti processes payments in paisa (1 NPR = 100 paisa).</div>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <h6><i class="bi bi-shield-check"></i> Secure Payment</h6>
                                    <p class="mb-0">Your payment details are protected with SSL encryption. Khalti does not share your financial information.</p>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn text-white btn-lg" style="background-color: #5c2d91;">
                                    <i class="bi bi-lock"></i> Proceed to Khalti Payment
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
                            <h6><i class="bi bi-question-circle"></i> How to pay with Khalti?</h6>
                            <ol class="mb-0">
                                <li>Enter product name and amount</li>
                                <li>Click "Proceed to Khalti Payment"</li>
                                <li>You will be redirected to Khalti</li>
                                <li>Login with your Khalti ID and MPIN</li>
                                <li>Confirm payment and enter OTP</li>
                                <li>You will be redirected back after success</li>
                            </ol>
                            <div class="mt-3">
                                <h6><i class="bi bi-lightbulb"></i> Test Credentials (Sandbox)</h6>
                                <ul class="mb-0">
                                    <li>Khalti ID: 9800000000</li>
                                    <li>MPIN: 1111</li>
                                    <li>OTP: 987654</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script>
        // Update paisa amount when NPR amount changes
        document.getElementById('amount').addEventListener('input', function() {
            const amountNpr = parseFloat(this.value) || 0;
            const amountPaisa = Math.round(amountNpr * 100);
            document.getElementById('amount_paisa').value = amountPaisa;
        });
    </script>
</body>
</html>
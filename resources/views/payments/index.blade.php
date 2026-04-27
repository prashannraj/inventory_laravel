<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Payment Gateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .gateway-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            height: 100%;
        }
        .gateway-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .gateway-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .esewa-color {
            color: #00a651;
        }
        .khalti-color {
            color: #5c2d91;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Select Payment Gateway</h3>
                    </div>
                    <div class="card-body">
                        <p class="lead">Choose your preferred payment method to proceed with the payment.</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-6 mb-4">
                                <a href="{{ route('esewa.checkout') }}" class="text-decoration-none">
                                    <div class="gateway-card">
                                        <div class="gateway-icon esewa-color">
                                            <i class="bi bi-credit-card"></i>
                                        </div>
                                        <h3 class="esewa-color">eSewa</h3>
                                        <p>Pay securely with eSewa - Nepal's leading digital wallet.</p>
                                        <div class="mt-3">
                                            <span class="badge bg-success">Secure</span>
                                            <span class="badge bg-info">Instant</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <a href="{{ route('khalti.checkout') }}" class="text-decoration-none">
                                    <div class="gateway-card">
                                        <div class="gateway-icon khalti-color">
                                            <i class="bi bi-wallet"></i>
                                        </div>
                                        <h3 class="khalti-color">Khalti</h3>
                                        <p>Pay with Khalti - Fast, easy and reliable payment solution.</p>
                                        <div class="mt-3">
                                            <span class="badge bg-success">Secure</span>
                                            <span class="badge bg-warning">Popular</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('payments.history') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-clock-history"></i> View Payment History
                            </a>
                            <a href="/" class="btn btn-link">Back to Home</a>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-center text-muted">
                    <small>All transactions are secured with encryption. Your payment details are safe.</small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>
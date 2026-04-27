<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error-icon {
            font-size: 80px;
            color: #dc3545;
        }
        .error-card {
            border: 1px solid #f5c6cb;
            border-radius: 10px;
            padding: 25px;
            background: #f8d7da;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h3 class="mb-0"><i class="bi bi-x-circle"></i> Payment Failed</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="error-icon mb-4">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <h2 class="text-danger">Payment Unsuccessful</h2>
                        
                        @if(session('error'))
                            <div class="alert alert-danger">
                                <h5><i class="bi bi-exclamation-triangle"></i> Error Details</h5>
                                <p class="mb-0">{{ session('error') }}</p>
                            </div>
                        @elseif(isset($error))
                            <div class="alert alert-danger">
                                <p class="mb-0">{{ $error }}</p>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <p class="mb-0">The payment could not be processed. Please try again.</p>
                            </div>
                        @endif
                        
                        <div class="error-card mt-4 text-start">
                            <h5><i class="bi bi-info-circle"></i> What could have happened?</h5>
                            <ul>
                                <li>Insufficient balance in your wallet</li>
                                <li>Incorrect payment details entered</li>
                                <li>Network connectivity issues</li>
                                <li>Payment gateway timeout</li>
                                <li>Transaction cancelled by user</li>
                            </ul>
                            <p class="mb-0">If you were charged but received this error, please contact our support team with your transaction ID.</p>
                        </div>
                        
                        <div class="mt-5">
                            <a href="{{ route('payment.index') }}" class="btn btn-primary">
                                <i class="bi bi-arrow-clockwise"></i> Try Again
                            </a>
                            <a href="{{ route('payments.history') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-clock-history"></i> View Payment History
                            </a>
                            <a href="/" class="btn btn-link">Back to Home</a>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-center text-muted">
                    <small>If the problem persists, please try a different payment method or contact support.</small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>
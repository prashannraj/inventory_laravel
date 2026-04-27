<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to eSewa...</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .spinner {
            width: 3rem;
            height: 3rem;
        }
        .redirect-card {
            max-width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow redirect-card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="bi bi-arrow-right-circle"></i> Redirecting to eSewa</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="spinner-border text-success spinner mb-4" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h4 class="text-success">Please wait...</h4>
                        <p class="lead">You are being redirected to eSewa's secure payment page.</p>
                        <p class="text-muted">Do not refresh or close this page.</p>
                        
                        <div class="alert alert-info text-start mt-4">
                            <h6><i class="bi bi-info-circle"></i> Payment Details</h6>
                            <ul class="mb-0">
                                <li><strong>Amount:</strong> NPR {{ number_format($formData['total_amount'] ?? 0, 2) }}</li>
                                <li><strong>Transaction ID:</strong> {{ $formData['transaction_uuid'] ?? 'N/A' }}</li>
                                <li><strong>Product:</strong> {{ $formData['product_name'] ?? 'N/A' }}</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <p class="text-muted">If you are not redirected automatically within 5 seconds, click the button below.</p>
                            <form id="esewaForm" method="POST" action="{{ $paymentUrl }}">
                                @foreach($formData as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-arrow-right"></i> Proceed to eSewa Now
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script>
        // Auto-submit form after 2 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.getElementById('esewaForm').submit();
            }, 2000);
        });
    </script>
</body>
</html>
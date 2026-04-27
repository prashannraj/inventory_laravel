<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay with eSewa - QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <style>
        .esewa-brand {
            color: #00a651;
            font-weight: bold;
        }
        .qr-container {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 30px;
            background: #fff;
            max-width: 500px;
            margin: 0 auto;
        }
        #qrcode {
            margin: 20px auto;
            text-align: center;
        }
        .payment-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .countdown {
            font-size: 1.2rem;
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0"><i class="fas fa-qrcode"></i> Scan QR Code to Pay with <span class="esewa-brand">eSewa</span></h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            Please scan the QR code below with your eSewa app to complete payment. 
                            The sale will be automatically completed once payment is received.
                        </div>

                        <div class="qr-container">
                            <h4 class="text-center mb-4">Payment Amount: <span class="text-success">NPR {{ number_format($amount, 2) }}</span></h4>
                            
                            <div id="qrcode"></div>
                            
                            <div class="text-center mt-3">
                                <small class="text-muted">Scan this QR code with eSewa app</small>
                            </div>

                            <div class="payment-details">
                                <h5><i class="fas fa-receipt"></i> Payment Details</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Invoice:</th>
                                        <td>{{ $productName }}</td>
                                    </tr>
                                    <tr>
                                        <th>Amount:</th>
                                        <td class="fw-bold">NPR {{ number_format($amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Transaction ID:</th>
                                        <td><code>{{ $payment->transaction_id }}</code></td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span class="badge bg-warning">Awaiting Payment</span>
                                            <div class="mt-2 countdown" id="countdown">Checking payment status...</div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="mt-4 text-center">
                                <p class="text-muted">
                                    <i class="fas fa-mobile-alt"></i> Open eSewa app → Scan QR Code → Confirm Payment
                                </p>
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="{{ route('sales.pos') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to POS
                                    </a>
                                    <a href="{{ $paymentUrl }}" class="btn btn-primary">
                                        <i class="fas fa-external-link-alt"></i> Pay Manually (Web)
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 alert alert-warning">
                            <h5><i class="fas fa-clock"></i> Payment Status</h5>
                            <p>This page will automatically check for payment status every 10 seconds. 
                               Once payment is confirmed, you will be redirected to the success page.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Generate QR Code
        const qrCodeData = @json($qrCodeData);
        const qrcode = new QRCode(document.getElementById("qrcode"), {
            text: qrCodeData,
            width: 250,
            height: 250,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        // Auto-check payment status
        let checkCount = 0;
        const maxChecks = 180; // 30 minutes (10 seconds * 180 = 1800 seconds = 30 minutes)
        
        function checkPaymentStatus() {
            checkCount++;
            document.getElementById('countdown').textContent = `Checking payment... (${checkCount})`;
            
            fetch(`/api/payment/status/{{ $payment->transaction_id }}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'completed') {
                        document.getElementById('countdown').innerHTML = 
                            '<span class="text-success"><i class="fas fa-check-circle"></i> Payment Received! Redirecting...</span>';
                        setTimeout(() => {
                            window.location.href = "{{ route('payment.success') }}";
                        }, 2000);
                    } else if (data.status === 'failed') {
                        document.getElementById('countdown').innerHTML = 
                            '<span class="text-danger"><i class="fas fa-times-circle"></i> Payment Failed</span>';
                        clearInterval(checkInterval);
                    } else if (checkCount >= maxChecks) {
                        document.getElementById('countdown').innerHTML = 
                            '<span class="text-warning"><i class="fas fa-clock"></i> Timeout - Please check payment manually</span>';
                        clearInterval(checkInterval);
                    }
                })
                .catch(error => {
                    console.error('Error checking payment:', error);
                });
        }

        // Check every 10 seconds
        const checkInterval = setInterval(checkPaymentStatus, 10000);
        
        // Initial check
        setTimeout(checkPaymentStatus, 2000);
    </script>
</body>
</html>
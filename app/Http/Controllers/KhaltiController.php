<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Services\KhaltiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KhaltiController extends Controller
{
    protected $khaltiService;

    public function __construct(KhaltiService $khaltiService)
    {
        $this->khaltiService = $khaltiService;
    }

    /**
     * Show the payment form with QR code.
     */
    public function showPaymentForm(Request $request)
    {
        // Get data from session (passed from SaleController)
        $saleId = session('sale_id');
        $paymentId = session('payment_id');
        $amount = session('amount');
        $productName = session('product_name');

        if (!$saleId || !$paymentId || !$amount) {
            return redirect()->route('sales.pos')->with('error', 'Invalid payment session. Please start over.');
        }

        // Get payment record
        $payment = Payment::find($paymentId);
        if (!$payment) {
            return redirect()->route('sales.pos')->with('error', 'Payment record not found.');
        }

        // Generate Khalti payment URL for QR code
        $transactionId = $payment->transaction_id;
        
        // For QR code, we can encode the payment URL or a deep link
        // Khalti QR code typically contains: khalti://payment?pidx=XYZ&amount=100
        $qrCodeData = $this->generateKhaltiQRCodeData($amount, $transactionId, $productName);

        return view('payments.khalti.qr', compact(
            'saleId', 'paymentId', 'amount', 'productName',
            'payment', 'qrCodeData'
        ));
    }

    /**
     * Generate Khalti QR code data string.
     */
    private function generateKhaltiQRCodeData($amount, $transactionId, $productName)
    {
        // Khalti deep link format (simplified)
        // In reality, Khalti uses pidx for payment identification
        // We'll generate a URL that can be opened in Khalti app
        return "khalti://payment?amount={$amount}&transaction_id={$transactionId}&product=" . urlencode($productName);
    }

    /**
     * Initiate payment and redirect to Khalti.
     */
    public function initiatePayment(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'product_name' => 'required|string|max:255',
        ]);

        $amount = $validated['amount'];
        $productName = $validated['product_name'];
        $orderId = $this->khaltiService->generateOrderId();

        // Create payment record
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'transaction_id' => $orderId,
            'payment_gateway' => 'khalti',
            'amount' => $amount,
            'status' => 'pending',
            'product_name' => $productName,
        ]);

        // Initiate payment with Khalti
        $result = $this->khaltiService->initiatePayment($amount, $orderId, $productName);

        if (!$result['success']) {
            $payment->update(['status' => 'failed']);
            Log::error('Khalti payment initiation failed', $result);
            return redirect()->route('payment.failed')->with('error', $result['message']);
        }

        // Save pidx to payment record
        $payment->update(['pidx' => $result['pidx']]);

        Log::info('Khalti payment initiated', [
            'order_id' => $orderId,
            'pidx' => $result['pidx'],
            'payment_id' => $payment->id,
        ]);

        // Redirect to Khalti payment page
        return redirect($result['payment_url']);
    }

    /**
     * Handle callback from Khalti.
     */
    public function handleCallback(Request $request)
    {
        try {
            $pidx = $request->query('pidx');
            $status = $request->query('status');
            $purchaseOrderId = $request->query('purchase_order_id');

            Log::info('Khalti callback received', $request->all());

            // Handle user cancellation
            if ($status === 'User canceled') {
                $payment = Payment::where('transaction_id', $purchaseOrderId)->first();
                if ($payment) {
                    $payment->update(['status' => 'failed']);
                    
                    // Also update SalePayment if exists
                    $salePayment = SalePayment::where('transaction_id', $purchaseOrderId)->first();
                    if ($salePayment) {
                        $salePayment->update([
                            'status' => 'failed',
                        ]);
                    }
                }
                return redirect()->route('payment.failed')->with('error', 'Payment was cancelled by user.');
            }

            if (!$pidx) {
                Log::error('Khalti callback missing pidx');
                return redirect()->route('payment.failed')->with('error', 'Invalid callback parameters.');
            }

            // Verify payment with Khalti
            $verificationResult = $this->khaltiService->verifyPayment($pidx);

            if (!$verificationResult['success']) {
                Log::error('Khalti payment verification failed', $verificationResult);
                return redirect()->route('payment.failed')->with('error', 'Payment verification failed.');
            }

            // Find payment record
            $payment = Payment::where('pidx', $pidx)
                ->orWhere('transaction_id', $purchaseOrderId)
                ->first();

            if (!$payment) {
                Log::error('Khalti payment record not found', ['pidx' => $pidx]);
                return redirect()->route('payment.failed')->with('error', 'Payment record not found.');
            }

            // Verify amount matches
            if ($payment->amount != $verificationResult['amount']) {
                Log::error('Khalti amount mismatch', [
                    'db_amount' => $payment->amount,
                    'verified_amount' => $verificationResult['amount'],
                ]);
                return redirect()->route('payment.failed')->with('error', 'Amount mismatch detected.');
            }

            // Update payment status
            $payment->update([
                'status' => 'completed',
                'gateway_response' => $verificationResult['data'],
            ]);

            // Find and update SalePayment
            $salePayment = SalePayment::where('transaction_id', $payment->transaction_id)->first();
            if ($salePayment) {
                $salePayment->update([
                    'status' => 'completed',
                ]);

                // Update Sale payment status
                $sale = $salePayment->sale;
                if ($sale) {
                    $sale->update([
                        'payment_status' => 'paid',
                        'status' => 'completed',
                        'paid_amount' => $sale->paid_amount + $payment->amount,
                    ]);
                }
            }

            Log::info('Khalti payment completed', [
                'pidx' => $pidx,
                'payment_id' => $payment->id,
                'sale_payment_id' => $salePayment?->id,
                'sale_id' => $sale?->id,
            ]);

            return redirect()->route('payment.success')->with([
                'success' => 'Payment completed successfully! Sale transaction has been completed.',
                'payment' => $payment,
                'sale' => $sale ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Khalti callback error', ['error' => $e->getMessage()]);
            return redirect()->route('payment.failed')->with('error', 'An error occurred while processing payment.');
        }
    }
}

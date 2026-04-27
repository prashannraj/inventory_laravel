<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Services\EsewaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EsewaController extends Controller
{
    protected $esewaService;

    public function __construct(EsewaService $esewaService)
    {
        $this->esewaService = $esewaService;
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

        // Generate eSewa payment URL for QR code
        $transactionId = $payment->transaction_id;
        $formData = $this->esewaService->getPaymentFormData($amount, $transactionId, $productName);
        $paymentUrl = $this->esewaService->getPaymentUrl();

        // For QR code, we can encode the payment URL or a deep link
        // eSewa QR code typically contains: esewa://pay?amt=100&pid=INV-123&scd=EPAYTEST&su=Success&fu=Failure
        $qrCodeData = $this->generateEsewaQRCodeData($amount, $transactionId, $productName);

        return view('payments.esewa.qr', compact(
            'saleId', 'paymentId', 'amount', 'productName',
            'payment', 'formData', 'paymentUrl', 'qrCodeData'
        ));
    }

    /**
     * Generate eSewa QR code data string.
     */
    private function generateEsewaQRCodeData($amount, $transactionId, $productName)
    {
        $merchantCode = $this->esewaService->getConfig('merchant_code');
        $successUrl = route('esewa.success');
        $failureUrl = route('esewa.failure');
        
        // eSewa deep link format
        return "esewa://pay?amt={$amount}&pid={$transactionId}&scd={$merchantCode}&su=" . urlencode($successUrl) . "&fu=" . urlencode($failureUrl);
    }

    /**
     * Initiate payment and redirect to eSewa.
     */
    public function initiatePayment(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'product_name' => 'required|string|max:255',
        ]);

        $amount = $validated['amount'];
        $productName = $validated['product_name'];
        $transactionId = $this->esewaService->generateTransactionId();

        // Create payment record
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'transaction_id' => $transactionId,
            'payment_gateway' => 'esewa',
            'amount' => $amount,
            'status' => 'pending',
            'product_name' => $productName,
        ]);

        // Get payment form data
        $formData = $this->esewaService->getPaymentFormData($amount, $transactionId, $productName);
        $paymentUrl = $this->esewaService->getPaymentUrl();

        Log::info('eSewa payment initiated', [
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'payment_id' => $payment->id,
        ]);

        return view('payments.esewa.redirect', compact('formData', 'paymentUrl'));
    }

    /**
     * Handle successful payment callback from eSewa.
     */
    public function paymentSuccess(Request $request)
    {
        try {
            $base64Data = $request->query('data');
            
            if (!$base64Data) {
                Log::error('eSewa success callback missing data parameter');
                return redirect()->route('payment.failed')->with('error', 'Invalid payment response.');
            }

            $verificationResult = $this->esewaService->verifyPayment($base64Data);

            if (!$verificationResult['success']) {
                Log::error('eSewa payment verification failed', $verificationResult);
                return redirect()->route('payment.failed')->with('error', 'Payment verification failed.');
            }

            $data = $verificationResult['data'];
            $transactionId = $data['transaction_uuid'] ?? null;
            $totalAmount = $data['total_amount'] ?? 0;

            // Find payment record
            $payment = Payment::where('transaction_id', $transactionId)->first();

            if (!$payment) {
                Log::error('eSewa payment record not found', ['transaction_id' => $transactionId]);
                return redirect()->route('payment.failed')->with('error', 'Payment record not found.');
            }

            // Update payment status
            $payment->update([
                'status' => 'completed',
                'gateway_response' => $data,
            ]);

            // Find and update SalePayment
            $salePayment = SalePayment::where('transaction_id', $transactionId)->first();
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

            Log::info('eSewa payment completed', [
                'transaction_id' => $transactionId,
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
            Log::error('eSewa success callback error', ['error' => $e->getMessage()]);
            return redirect()->route('payment.failed')->with('error', 'An error occurred while processing payment.');
        }
    }

    /**
     * Handle failed payment callback from eSewa.
     */
    public function paymentFailure(Request $request)
    {
        Log::warning('eSewa payment failure', $request->all());

        $transactionId = $request->query('transaction_uuid');
        
        if ($transactionId) {
            $payment = Payment::where('transaction_id', $transactionId)->first();
            if ($payment) {
                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => $request->all(),
                ]);

                // Also update SalePayment if exists
                $salePayment = SalePayment::where('transaction_id', $transactionId)->first();
                if ($salePayment) {
                    $salePayment->update([
                        'status' => 'failed',
                    ]);
                }
            }
        }

        return redirect()->route('payment.failed')->with('error', 'Payment was cancelled or failed.');
    }
}

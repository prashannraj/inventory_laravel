<?php

namespace App\Services;

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EsewaService
{
    /**
     * The payment method instance.
     *
     * @var PaymentMethod|null
     */
    protected $paymentMethod = null;

    /**
     * Get the payment method for eSewa.
     *
     * @return PaymentMethod|null
     */
    protected function getPaymentMethod()
    {
        if ($this->paymentMethod === null) {
            $this->paymentMethod = PaymentMethod::where('gateway', 'esewa')
                ->where('active', true)
                ->first();
        }
        return $this->paymentMethod;
    }

    /**
     * Get configuration value, first from payment method details, then from config.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getConfig($key, $default = null)
    {
        $paymentMethod = $this->getPaymentMethod();
        if ($paymentMethod && $paymentMethod->details) {
            $details = $paymentMethod->details;
            if (is_array($details) && array_key_exists($key, $details)) {
                return $details[$key];
            }
        }
        return config("esewa.{$key}", $default);
    }
    /**
     * Generate a unique transaction ID.
     *
     * @return string
     */
    public function generateTransactionId(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Generate HMAC SHA256 signature for eSewa.
     *
     * @param string $message
     * @return string
     */
    public function generateSignature(string $message): string
    {
        $secretKey = $this->getConfig('secret_key');
        return base64_encode(hash_hmac('sha256', $message, $secretKey, true));
    }

    /**
     * Get payment form data for eSewa.
     *
     * @param float $amount
     * @param string $transactionId
     * @param string $productName
     * @return array
     */
    public function getPaymentFormData(float $amount, string $transactionId, string $productName): array
    {
        $merchantCode = $this->getConfig('merchant_code');
        $taxAmount = $this->getConfig('tax_amount', 0);
        $serviceCharge = $this->getConfig('product_service_charge', 0);
        $deliveryCharge = $this->getConfig('product_delivery_charge', 0);
        $totalAmount = $amount + $taxAmount + $serviceCharge + $deliveryCharge;

        // Create signature message
        $message = "total_amount={$totalAmount},transaction_uuid={$transactionId},product_code={$merchantCode}";
        $signature = $this->generateSignature($message);

        return [
            'amount' => $amount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'transaction_uuid' => $transactionId,
            'product_code' => $merchantCode,
            'product_service_charge' => $serviceCharge,
            'product_delivery_charge' => $deliveryCharge,
            'product_name' => $productName,
            'success_url' => $this->getConfig('success_url'),
            'failure_url' => $this->getConfig('failure_url'),
            'signed_field_names' => $this->getConfig('signed_field_names'),
            'signature' => $signature,
        ];
    }

    /**
     * Verify payment using base64 encoded response from eSewa.
     *
     * @param string $base64Data
     * @return array
     */
    public function verifyPayment(string $base64Data): array
    {
        try {
            $decodedData = base64_decode($base64Data);
            parse_str($decodedData, $response);

            Log::info('eSewa payment response', $response);

            if (!isset($response['status']) || $response['status'] !== 'COMPLETE') {
                return [
                    'success' => false,
                    'message' => 'Payment not completed',
                    'data' => $response,
                ];
            }

            // Verify with server
            $verification = $this->verifyWithServer(
                $response['transaction_uuid'] ?? '',
                $response['total_amount'] ?? 0
            );

            if (!$verification['success']) {
                return [
                    'success' => false,
                    'message' => 'Server verification failed',
                    'data' => $response,
                    'verification' => $verification,
                ];
            }

            return [
                'success' => true,
                'message' => 'Payment verified successfully',
                'data' => $response,
                'verification' => $verification,
            ];
        } catch (\Exception $e) {
            Log::error('eSewa payment verification error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error verifying payment: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Verify payment with eSewa server.
     *
     * @param string $transactionId
     * @param float $totalAmount
     * @return array
     */
    public function verifyWithServer(string $transactionId, float $totalAmount): array
    {
        try {
            $merchantCode = $this->getConfig('merchant_code');
            $verificationUrl = $this->getConfig('verification_url');

            $response = Http::timeout(30)
                ->get($verificationUrl, [
                    'product_code' => $merchantCode,
                    'total_amount' => $totalAmount,
                    'transaction_uuid' => $transactionId,
                ]);

            $data = $response->json();

            Log::info('eSewa server verification response', $data);

            if ($response->successful() && isset($data['status']) && $data['status'] === 'COMPLETE') {
                return [
                    'success' => true,
                    'message' => 'Server verification successful',
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => 'Server verification failed',
                'data' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('eSewa server verification error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Server verification error: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Get eSewa payment URL.
     *
     * @return string
     */
    public function getPaymentUrl(): string
    {
        return $this->getConfig('payment_url');
    }
}
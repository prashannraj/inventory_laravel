<?php

namespace App\Services;

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KhaltiService
{
    /**
     * The payment method instance.
     *
     * @var PaymentMethod|null
     */
    protected $paymentMethod = null;

    /**
     * Get the payment method for Khalti.
     *
     * @return PaymentMethod|null
     */
    protected function getPaymentMethod()
    {
        if ($this->paymentMethod === null) {
            $this->paymentMethod = PaymentMethod::where('gateway', 'khalti')
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
        return config("khalti.{$key}", $default);
    }
    /**
     * Generate a unique order ID.
     *
     * @return string
     */
    public function generateOrderId(): string
    {
        return 'KHLTI' . Str::random(10) . time();
    }

    /**
     * Get the appropriate secret key based on environment.
     *
     * @return string
     */
    private function getSecretKey(): string
    {
        $testMode = $this->getConfig('test_mode', true);
        return $testMode
            ? $this->getConfig('secret_key_test')
            : $this->getConfig('secret_key');
    }

    /**
     * Get the base URL for API requests.
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        return $this->getConfig('base_url');
    }

    /**
     * Initiate payment with Khalti.
     *
     * @param float $amount
     * @param string $orderId
     * @param string $orderName
     * @param array|null $customerInfo
     * @return array
     */
    public function initiatePayment(float $amount, string $orderId, string $orderName, ?array $customerInfo = null): array
    {
        try {
            $secretKey = $this->getSecretKey();
            $initiateUrl = $this->getBaseUrl() . 'epayment/initiate/';

            // Convert amount to paisa
            $amountInPaisa = $amount * $this->getConfig('currency_multiplier', 100);

            $payload = [
                'return_url' => $this->getConfig('return_url'),
                'website_url' => $this->getConfig('website_url'),
                'amount' => $amountInPaisa,
                'purchase_order_id' => $orderId,
                'purchase_order_name' => $orderName,
                'customer_info' => $customerInfo ?? [
                    'name' => 'Customer',
                    'email' => 'customer@example.com',
                    'phone' => '9800000000',
                ],
            ];

            Log::info('Khalti initiate payment payload', $payload);

            $response = Http::timeout($this->getConfig('timeout', 30))
                ->withHeaders([
                    'Authorization' => 'Key ' . $secretKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($initiateUrl, $payload);

            $data = $response->json();

            Log::info('Khalti initiate payment response', $data);

            if ($response->successful() && isset($data['pidx']) && isset($data['payment_url'])) {
                return [
                    'success' => true,
                    'message' => 'Payment initiated successfully',
                    'payment_url' => $data['payment_url'],
                    'pidx' => $data['pidx'],
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $data['detail'] ?? 'Failed to initiate payment',
                'payment_url' => null,
                'pidx' => null,
                'data' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Khalti initiate payment error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error initiating payment: ' . $e->getMessage(),
                'payment_url' => null,
                'pidx' => null,
                'data' => [],
            ];
        }
    }

    /**
     * Verify payment with Khalti using pidx.
     *
     * @param string $pidx
     * @return array
     */
    public function verifyPayment(string $pidx): array
    {
        try {
            $secretKey = $this->getSecretKey();
            $lookupUrl = $this->getBaseUrl() . 'epayment/lookup/';

            $response = Http::timeout($this->getConfig('timeout', 30))
                ->withHeaders([
                    'Authorization' => 'Key ' . $secretKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($lookupUrl, [
                    'pidx' => $pidx,
                ]);

            $data = $response->json();

            Log::info('Khalti verify payment response', $data);

            if ($response->successful() && isset($data['status'])) {
                $amountInPaisa = $data['total_amount'] ?? 0;
                $amountInNpr = $amountInPaisa / $this->getConfig('currency_multiplier', 100);

                $result = [
                    'success' => $data['status'] === 'Completed',
                    'status' => $data['status'],
                    'message' => $data['status'] === 'Completed' ? 'Payment completed' : 'Payment not completed',
                    'transaction_id' => $data['transaction_id'] ?? null,
                    'pidx' => $data['pidx'] ?? $pidx,
                    'amount' => $amountInNpr,
                    'purchase_order_id' => $data['purchase_order_id'] ?? null,
                    'data' => $data,
                ];

                return $result;
            }

            return [
                'success' => false,
                'message' => $data['detail'] ?? 'Verification failed',
                'status' => null,
                'transaction_id' => null,
                'pidx' => $pidx,
                'amount' => 0,
                'purchase_order_id' => null,
                'data' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Khalti verify payment error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error verifying payment: ' . $e->getMessage(),
                'status' => null,
                'transaction_id' => null,
                'pidx' => $pidx,
                'amount' => 0,
                'purchase_order_id' => null,
                'data' => [],
            ];
        }
    }

    /**
     * Convert amount from NPR to paisa.
     *
     * @param float $amountNpr
     * @return int
     */
    public function toPaisa(float $amountNpr): int
    {
        return (int) ($amountNpr * $this->getConfig('currency_multiplier', 100));
    }

    /**
     * Convert amount from paisa to NPR.
     *
     * @param int $amountPaisa
     * @return float
     */
    public function toNpr(int $amountPaisa): float
    {
        return $amountPaisa / $this->getConfig('currency_multiplier', 100);
    }
}
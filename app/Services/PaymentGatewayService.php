<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    /**
     * Trigger payout disbursement to agent bank account.
     */
    public function disburseCommission(string $payoutId, string $bankCode, string $accountNumber, float $amount): array
    {
        $serverKey = SystemSetting::where('key', 'payment_gateway_server_key')->value('value');

        // Mock payment gateway in local/testing
        if (app()->environment('local', 'testing') || empty($serverKey) || $serverKey === 'SB-Mid-server-BPKH2025Secret') {
            return [
                'success' => true,
                'status' => 'COMPLETED',
                'reference_id' => 'PG-MOCK-' . uniqid(),
                'message' => 'Disbursement processed successfully (Mock).'
            ];
        }

        try {
            // Send payout request to the gateway (example: Xendit/Midtrans Iris API)
            $response = Http::withBasicAuth($serverKey, '')
                ->post('https://api.xendit.co/disbursements', [
                    'reference_id' => $payoutId,
                    'bank_code' => $bankCode,
                    'account_holder_name' => 'Agent Account',
                    'account_number' => $accountNumber,
                    'amount' => $amount,
                    'description' => 'BPKH Hajj Agent Commission Payout'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'status' => $data['status'] ?? 'PENDING',
                    'reference_id' => $data['id'] ?? null,
                    'message' => 'Disbursement request sent successfully.'
                ];
            }

            return [
                'success' => false,
                'message' => 'Payment gateway error: ' . $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Payment Gateway Payout Connection Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to connect to payout gateway.'
            ];
        }
    }
}

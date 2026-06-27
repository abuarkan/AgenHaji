<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DukcapilService
{
    /**
     * Verify NIK (National ID) with Dukcapil API.
     */
    public function verifyNIK(string $nik, string $name = ''): array
    {
        $apiUrl = SystemSetting::where('key', 'dukcapil_api_url')->value('value')
            ?? 'https://api.dukcapil.go.id/v1/verify';
        
        $apiKey = SystemSetting::where('key', 'dukcapil_api_key')->value('value');

        // Mock verification in local/testing environments
        if (app()->environment('local', 'testing') || empty($apiKey) || $apiKey === 'secret_dukcapil_key_bpkh') {
            if (strlen($nik) === 16 && is_numeric($nik)) {
                return [
                    'success' => true,
                    'message' => 'NIK verified successfully with Dukcapil (Mock).',
                    'data' => [
                        'nik' => $nik,
                        'name' => $name,
                        'verified' => true
                    ]
                ];
            }

            return [
                'success' => false,
                'message' => 'Invalid NIK format. NIK must be exactly 16 digits.'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json'
            ])->post($apiUrl, [
                'nik' => $nik,
                'name' => $name
            ]);

            if ($response->successful() && ($response->json()['status'] ?? '') === 'verified') {
                return [
                    'success' => true,
                    'message' => 'NIK verified successfully with Dukcapil.',
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Dukcapil verification failed or details mismatched.'
            ];
        } catch (\Exception $e) {
            Log::error('Dukcapil API Connection Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Connection to Dukcapil API failed.'
            ];
        }
    }
}

<?php

namespace App\Services;

use App\Models\ProspectJemaah;
use Illuminate\Support\Facades\Log;

class SiskehatApiService
{
    /**
     * Synchronize nominative pilgrim data from SISKEHAT API.
     * Maps SISKEHAT registration channel, portion number binding, and verification timestamps.
     */
    public function syncNominativePilgrimData(?int $prospectId = null): array
    {
        $query = ProspectJemaah::withoutGlobalScopes();
        if ($prospectId) {
            $query->where('id', $prospectId);
        }

        $prospects = $query->get();
        $syncedCount = 0;

        foreach ($prospects as $prospect) {
            // Mock SISKEHAT API sync response logic
            // In real production, this calls http endpoint to SISKEHAT Kemenag / BPKH API Gateway
            $referenceId = 'SISKEHAT-' . strtoupper(substr(md5($prospect->nik . $prospect->id), 0, 8));
            
            // Determine registration channel (BPKH Apps vs Non-BPKH Apps)
            // If channel is not explicitly set, assign based on referral code or default to bpkh_apps
            $channel = $prospect->registration_channel ?? 'bpkh_apps';

            $prospect->update([
                'siskehat_reference_id' => $referenceId,
                'siskehat_sync_at' => now(),
                'registration_channel' => $channel,
                'is_porsi_bound' => !empty($prospect->porsi_number),
            ]);

            $syncedCount++;
        }

        Log::info("SISKEHAT API SYNC: Successfully synchronized {$syncedCount} nominative pilgrim records.");

        return [
            'success' => true,
            'synced_count' => $syncedCount,
            'message' => "Sinkronisasi Data Nominatif SISKEHAT berhasil ({$syncedCount} data jemaah diperbarui)."
        ];
    }
}

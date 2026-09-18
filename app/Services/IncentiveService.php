<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\ProspectJemaah;
use App\Models\ReferralProgram;
use App\Models\SystemSetting;
use Carbon\Carbon;

class IncentiveService
{
    /**
     * Get configured incentive rates per tier and channel from SystemSettings.
     */
    public function getIncentiveRates(): array
    {
        $settings = SystemSetting::all()->pluck('value', 'key')->toArray();

        return [
            'bpkh_apps' => [
                'silver' => intval($settings['incentive_bpkh_silver'] ?? 125000),
                'gold' => intval($settings['incentive_bpkh_gold'] ?? 150000),
                'platinum' => intval($settings['incentive_bpkh_platinum'] ?? 175000),
                'diamond' => intval($settings['incentive_bpkh_diamond'] ?? 200000),
            ],
            'non_bpkh_apps' => [
                'silver' => intval($settings['incentive_non_bpkh_silver'] ?? 100000),
                'gold' => intval($settings['incentive_non_bpkh_gold'] ?? 125000),
                'platinum' => intval($settings['incentive_non_bpkh_platinum'] ?? 150000),
                'diamond' => intval($settings['incentive_non_bpkh_diamond'] ?? 175000),
            ]
        ];
    }

    /**
     * Calculate progressive tier incentive breakdown for an agent for a specific month/year.
     */
    public function calculateMonthlyAgentIncentive(int $agentId, ?int $month = null, ?int $year = null): array
    {
        $month = $month ?? Carbon::now()->month;
        $year = $year ?? Carbon::now()->year;

        $rates = $this->getIncentiveRates();

        // Query verified prospects with portion binding registered in the given month/year
        $prospects = ProspectJemaah::withoutGlobalScopes()
            ->where('agent_id', $agentId)
            ->whereIn('status_pendaftaran', ['Verified', 'Pendaftar Haji'])
            ->whereNotNull('porsi_number')
            ->where('porsi_number', '!=', '')
            ->where('is_porsi_bound', true)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $bpkhProspectsCount = $prospects->where('registration_channel', 'bpkh_apps')->count();
        $nonBpkhProspectsCount = $prospects->where('registration_channel', 'non_bpkh_apps')->count();
        $totalPorsiCount = $prospects->count();

        // Progressive Tiering calculation for BPKH Apps
        $bpkhBreakdown = $this->computeTierBreakdown($bpkhProspectsCount, $rates['bpkh_apps']);
        
        // Progressive Tiering calculation for Non-BPKH Apps
        $nonBpkhBreakdown = $this->computeTierBreakdown($nonBpkhProspectsCount, $rates['non_bpkh_apps']);

        $totalIncentiveAmount = $bpkhBreakdown['total_amount'] + $nonBpkhBreakdown['total_amount'];

        return [
            'month' => $month,
            'year' => $year,
            'period_label' => Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y'),
            'total_porsi' => $totalPorsiCount,
            'bpkh_apps' => [
                'count' => $bpkhProspectsCount,
                'breakdown' => $bpkhBreakdown,
            ],
            'non_bpkh_apps' => [
                'count' => $nonBpkhProspectsCount,
                'breakdown' => $nonBpkhBreakdown,
            ],
            'total_incentive_amount' => $totalIncentiveAmount,
            'payout_estimation_days' => 14,
            'payout_method' => 'M-Pocket BPKH Apps',
        ];
    }

    /**
     * Compute progressive tier calculation given portion count and rate parameters.
     * Tiers: Silver (1-20), Gold (21-35), Platinum (36-50), Diamond (>50)
     */
    private function computeTierBreakdown(int $count, array $tierRates): array
    {
        $silverCount = min(20, max(0, $count));
        $goldCount = min(15, max(0, $count - 20));
        $platinumCount = min(15, max(0, $count - 35));
        $diamondCount = max(0, $count - 50);

        $silverAmount = $silverCount * $tierRates['silver'];
        $goldAmount = $goldCount * $tierRates['gold'];
        $platinumAmount = $platinumCount * $tierRates['platinum'];
        $diamondAmount = $diamondCount * $tierRates['diamond'];

        $totalAmount = $silverAmount + $goldAmount + $platinumAmount + $diamondAmount;

        return [
            'silver' => ['count' => $silverCount, 'rate' => $tierRates['silver'], 'amount' => $silverAmount],
            'gold' => ['count' => $goldCount, 'rate' => $tierRates['gold'], 'amount' => $goldAmount],
            'platinum' => ['count' => $platinumCount, 'rate' => $tierRates['platinum'], 'amount' => $platinumAmount],
            'diamond' => ['count' => $diamondCount, 'rate' => $tierRates['diamond'], 'amount' => $diamondAmount],
            'total_amount' => $totalAmount,
        ];
    }
}

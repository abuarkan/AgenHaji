<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\AgentPointLedger;
use App\Models\ProspectJemaah;
use App\Models\RacingProgram;
use App\Models\ReferralProgram;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    /**
     * Check whether referral program is active and monetizable for a given date.
     */
    public function isReferralMonetizable($date = null): bool
    {
        return ReferralProgram::activeOnDate($date)->exists();
    }

    /**
     * Get active referral program details.
     */
    public function getActiveReferralProgram($date = null): ?ReferralProgram
    {
        return ReferralProgram::activeOnDate($date)->first();
    }

    /**
     * Award points to an agent when a prospect is verified or portion number assigned.
     */
    public function awardPointsForProspect(ProspectJemaah $prospect, string $event = 'verified'): ?AgentPointLedger
    {
        $agent = $prospect->agent;
        if (!$agent) {
            return null;
        }

        $pointsSettingKey = 'points_per_reguler';
        $defaultPoints = 10;
        $description = "Poin Referal Jemaah Haji ({$prospect->name})";

        if ($event === 'portion') {
            $pointsSettingKey = 'points_per_portion';
            $defaultPoints = 5;
            $description = "Bonus Porsi Haji Terbit ({$prospect->name})";

            // Check if bonus for portion already awarded for this prospect
            $alreadyAwarded = AgentPointLedger::where('agent_id', $agent->id)
                ->where('prospect_jemaah_id', $prospect->id)
                ->where('description', 'like', 'Bonus Porsi%')
                ->exists();

            if ($alreadyAwarded) {
                return null;
            }
        } else {
            // Check if initial registration points already awarded
            $alreadyAwarded = AgentPointLedger::where('agent_id', $agent->id)
                ->where('prospect_jemaah_id', $prospect->id)
                ->where('description', 'like', 'Poin Referal%')
                ->exists();

            if ($alreadyAwarded) {
                return null;
            }

            if (str_contains(strtolower($prospect->registration_type ?? ''), 'khusus') || str_contains(strtolower($prospect->registration_type ?? ''), 'plus')) {
                $pointsSettingKey = 'points_per_khusus';
                $defaultPoints = 25;
            }
        }

        $pointsSetting = SystemSetting::where('key', $pointsSettingKey)->first();
        $points = $pointsSetting ? (int)$pointsSetting->value : $defaultPoints;

        return AgentPointLedger::create([
            'agent_id' => $agent->id,
            'prospect_jemaah_id' => $prospect->id,
            'type' => 'earned',
            'points' => $points,
            'description' => $description,
        ]);
    }

    /**
     * Get total points for a specific agent.
     */
    public function getAgentTotalPoints(int $agentId): int
    {
        $earned = AgentPointLedger::where('agent_id', $agentId)
            ->where('type', 'earned')
            ->sum('points');

        $redeemed = AgentPointLedger::where('agent_id', $agentId)
            ->where('type', 'redeemed')
            ->sum('points');

        return max(0, $earned - $redeemed);
    }

    /**
     * Get racing competition leaderboard for active or specified racing program.
     */
    public function getRacingLeaderboard(?RacingProgram $program = null, ?Agent $currentAgent = null): array
    {
        if (!$program) {
            $program = RacingProgram::activeOnDate()->first();
        }

        if (!$program) {
            return [
                'has_active_program' => false,
                'program' => null,
                'leaderboard' => [],
                'current_agent_rank' => null,
            ];
        }

        // Fetch agents with portion counts during the racing period
        $agents = Agent::with(['user', 'level'])
            ->where('status', 'active')
            ->get();

        $minThreshold = $program->min_portion_target ?? 100;
        $winnerQuota = $program->winner_quota ?? 6;

        $leaderboardData = [];

        foreach ($agents as $ag) {
            $startDateStr = \Carbon\Carbon::parse($program->start_date)->format('Y-m-d');
            $endDateStr = \Carbon\Carbon::parse($program->end_date)->format('Y-m-d');

            $portionCount = ProspectJemaah::withoutGlobalScopes()
                ->where('agent_id', $ag->id)
                ->whereNotNull('porsi_number')
                ->where('porsi_number', '!=', '')
                ->where('is_porsi_bound', true)
                ->whereDate('created_at', '>=', $startDateStr)
                ->whereDate('created_at', '<=', $endDateStr)
                ->count();

            $isQualified = $portionCount >= $minThreshold;

            $leaderboardData[] = [
                'agent_id' => $ag->id,
                'name' => $ag->user ? $ag->user->name : 'Unknown',
                'referral_code' => $ag->referral_code,
                'level' => $ag->level ? $ag->level->name : 'Silver',
                'portion_count' => $portionCount,
                'min_threshold' => $minThreshold,
                'is_qualified' => $isQualified,
                'needed_to_threshold' => max(0, $minThreshold - $portionCount),
                'is_umrah_winner' => false,
            ];
        }

        // Sort: Qualified agents (passed threshold) first, then by portion count descending
        usort($leaderboardData, function ($a, $b) {
            if ($a['is_qualified'] !== $b['is_qualified']) {
                return $b['is_qualified'] <=> $a['is_qualified'];
            }
            return $b['portion_count'] <=> $a['portion_count'];
        });

        // Assign ranks and Umrah reward winners (Top winner_quota qualified agents)
        $rank = 1;
        $currentAgentRank = null;
        $winnerCount = 0;

        foreach ($leaderboardData as $index => &$item) {
            $item['rank'] = $rank++;
            if ($item['is_qualified'] && $winnerCount < $winnerQuota) {
                $item['is_umrah_winner'] = true;
                $winnerCount++;
            }

            if ($currentAgent && $item['agent_id'] === $currentAgent->id) {
                $currentAgentRank = $item;
            }
        }

        return [
            'has_active_program' => true,
            'program' => $program,
            'leaderboard' => array_slice($leaderboardData, 0, 10), // Top 10
            'all_standings' => $leaderboardData,
            'current_agent_rank' => $currentAgentRank,
            'winner_count' => $winnerCount,
            'winner_quota' => $winnerQuota,
        ];
    }
}

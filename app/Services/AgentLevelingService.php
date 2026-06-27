<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\AgentLevel;
use App\Models\AuditLog;
use App\Models\CommissionLedger;
use App\Models\ProspectJemaah;
use App\Repositories\AgentRepository;
use App\Repositories\ProspectRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgentLevelingService
{
    protected $agentRepo;
    protected $prospectRepo;

    public function __construct(AgentRepository $agentRepo, ProspectRepository $prospectRepo)
    {
        $this->agentRepo = $agentRepo;
        $this->prospectRepo = $prospectRepo;
    }

    /**
     * Evaluate and update a single agent's level based on verified prospects.
     */
    public function evaluateAgentLevel(Agent $agent): array
    {
        // 1. Count verified prospects for the agent
        $verifiedCount = $this->prospectRepo->getVerifiedCountForAgent($agent->id);

        // 2. Fetch all levels from database sorted by target descending
        $levels = AgentLevel::orderBy('target_prospects', 'desc')->get();

        // 3. Find the highest qualified level
        $qualifiedLevel = null;
        foreach ($levels as $lvl) {
            if ($verifiedCount >= $lvl->target_prospects) {
                $qualifiedLevel = $lvl;
                break;
            }
        }

        // If verified count is below the lowest level target, keep them at lowest level
        if (!$qualifiedLevel) {
            $qualifiedLevel = $levels->last(); // Default fallback to Silver
        }

        $oldLevel = $agent->level;
        $levelChanged = false;

        if ($agent->agent_level_id !== $qualifiedLevel->id) {
            $levelChanged = true;

            // Start database transaction to ensure transactional integrity
            DB::transaction(function () use ($agent, $qualifiedLevel, $oldLevel, $verifiedCount) {
                // Update agent level
                $agent->update([
                    'agent_level_id' => $qualifiedLevel->id
                ]);

                // Create Audit Log entry
                AuditLog::create([
                    'user_id' => auth()->id() ?? $agent->user_id,
                    'action' => 'agent_level_promotion',
                    'model_type' => Agent::class,
                    'model_id' => $agent->id,
                    'ip_address' => request()->ip() ?? '127.0.0.1',
                    'before_payload' => [
                        'level_id' => $oldLevel->id,
                        'level_name' => $oldLevel->name,
                        'verified_prospects_count' => $verifiedCount,
                    ],
                    'after_payload' => [
                        'level_id' => $qualifiedLevel->id,
                        'level_name' => $qualifiedLevel->name,
                        'verified_prospects_count' => $verifiedCount,
                    ]
                ]);

                // Log promotion for WhatsApp/Email notification scheduling
                Log::info("NOTIFICATION: Agent {$agent->user->name} promoted from {$oldLevel->name} to {$qualifiedLevel->name}. Verified prospects count: {$verifiedCount}");
            });
        }

        return [
            'agent_id' => $agent->id,
            'verified_count' => $verifiedCount,
            'old_level' => $oldLevel ? $oldLevel->name : 'None',
            'new_level' => $qualifiedLevel->name,
            'level_changed' => $levelChanged
        ];
    }

    /**
     * Evaluate and update levels for all active agents.
     */
    public function evaluateAllAgents(): array
    {
        $agents = $this->agentRepo->getActiveAgents();
        $results = [];

        foreach ($agents as $agent) {
            $results[] = $this->evaluateAgentLevel($agent);
        }

        return $results;
    }

    /**
     * Credit commission to an agent for a verified prospect.
     */
    public function creditCommissionForProspect(ProspectJemaah $prospect): ?CommissionLedger
    {
        if (!in_array($prospect->status_pendaftaran, ['Verified', 'Pendaftar Haji'])) {
            return null;
        }

        // Prevent double crediting for the same prospect
        $existing = CommissionLedger::where('prospect_jemaah_id', $prospect->id)
            ->where('type', 'credit')
            ->first();

        if ($existing) {
            return $existing;
        }

        $agent = $prospect->agent;
        $level = $agent->level;

        return DB::transaction(function () use ($agent, $prospect, $level) {
            $currentBalance = $agent->commission_balance;
            $commissionAmount = $level->commission_per_prospect;
            $newBalance = $currentBalance + $commissionAmount;

            // Create ledger entry
            $ledger = CommissionLedger::create([
                'agent_id' => $agent->id,
                'prospect_jemaah_id' => $prospect->id,
                'type' => 'credit',
                'amount' => $commissionAmount,
                'balance_after' => $newBalance,
                'status' => 'approved',
            ]);

            // Create Audit Log for commission earning
            AuditLog::create([
                'user_id' => null,
                'action' => 'commission_earned',
                'model_type' => CommissionLedger::class,
                'model_id' => $ledger->id,
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'before_payload' => [
                    'agent_id' => $agent->id,
                    'prospect_id' => $prospect->id,
                    'agent_level' => $level->name,
                    'commission_rate' => $commissionAmount,
                    'previous_balance' => $currentBalance
                ],
                'after_payload' => [
                    'ledger_id' => $ledger->id,
                    'amount_credited' => $commissionAmount,
                    'new_balance' => $newBalance
                ]
            ]);

            return $ledger;
        });
    }
}

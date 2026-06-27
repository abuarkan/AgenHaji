<?php

namespace App\Repositories;

use App\Models\Agent;
use Illuminate\Database\Eloquent\Collection;

class AgentRepository
{
    /**
     * Get all active agents.
     */
    public function getActiveAgents(): Collection
    {
        return Agent::where('status', 'active')->get();
    }

    /**
     * Find agent by ID.
     */
    public function findById(int $id): ?Agent
    {
        return Agent::find($id);
    }

    /**
     * Find agent by referral code.
     */
    public function findByReferralCode(string $code): ?Agent
    {
        return Agent::where('referral_code', $code)->first();
    }

    /**
     * Update agent level.
     */
    public function updateLevel(Agent $agent, int $levelId): bool
    {
        return $agent->update([
            'agent_level_id' => $levelId
        ]);
    }
}

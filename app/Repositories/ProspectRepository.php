<?php

namespace App\Repositories;

use App\Models\ProspectJemaah;
use Illuminate\Database\Eloquent\Collection;

class ProspectRepository
{
    /**
     * Get count of verified prospects for a specific agent.
     */
    public function getVerifiedCountForAgent(int $agentId): int
    {
        return ProspectJemaah::where('agent_id', $agentId)
            ->whereIn('status_pendaftaran', ['Verified', 'Pendaftar Haji'])
            ->count();
    }

    /**
     * Get all prospects for an agent.
     */
    public function getByAgentId(int $agentId): Collection
    {
        return ProspectJemaah::where('agent_id', $agentId)->get();
    }

    /**
     * Get filtered and paginated prospects.
     */
    public function getFilteredAndPaginated(array $filters = [], int $perPage = 10)
    {
        $query = ProspectJemaah::query();

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            if ($filters['status'] === 'Pendaftar Haji') {
                $query->whereIn('status_pendaftaran', ['Pendaftar Haji', 'Verified']);
            } elseif ($filters['status'] === 'Tertarik Daftar Haji') {
                $query->where('status_pendaftaran', 'Tertarik Daftar Haji');
            } else {
                $query->where('status_pendaftaran', $filters['status']);
            }
        }

        if (!empty($filters['search_name'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search_name'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search_name'] . '%');
            });
        }

        if (!empty($filters['search_bank'])) {
            $query->where('bps_bpih', 'like', '%' . $filters['search_bank'] . '%');
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }
}

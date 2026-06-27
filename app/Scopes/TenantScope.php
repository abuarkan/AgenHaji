<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Skip scope application in console unless explicitly authenticated
        if (app()->runningInConsole() && !Auth::check()) {
            return;
        }

        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();

        // Superadmin and Admin Haji bypass tenant scopes (sees everything nationwide)
        if ($user->role === 'superadmin' || $user->role === 'admin_haji') {
            return;
        }

        // Fetch agent without global scopes to prevent infinite recursion
        if (!$user->relationLoaded('agent')) {
            $agent = \App\Models\Agent::withoutGlobalScope(self::class)
                ->where('user_id', $user->id)
                ->first();
            $user->setRelation('agent', $agent);
        }

        $agent = $user->getRelation('agent');

        if (!$agent) {
            // If authenticated user is an agent role but has no profile, restrict all records
            $builder->whereRaw('1 = 0');
            return;
        }

        $tableName = $model->getTable();

        // Institutional Agent: Can view data for all agents within their institution
        if ($agent->type === 'institution' && $agent->institution_id) {
            if ($tableName === 'prospect_jemaahs') {
                $builder->whereIn('agent_id', function ($query) use ($agent) {
                    $query->select('id')
                          ->from('agents')
                          ->where('institution_id', $agent->institution_id);
                });
            } elseif ($tableName === 'agents') {
                $builder->where('institution_id', $agent->institution_id);
            } elseif ($tableName === 'commission_ledgers') {
                $builder->whereIn('agent_id', function ($query) use ($agent) {
                    $query->select('id')
                          ->from('agents')
                          ->where('institution_id', $agent->institution_id);
                });
            }
        } else {
            // Freelance Agent: Can only view their own data
            if ($tableName === 'prospect_jemaahs') {
                $builder->where('agent_id', $agent->id);
            } elseif ($tableName === 'agents') {
                $builder->where('id', $agent->id);
            } elseif ($tableName === 'commission_ledgers') {
                $builder->where('agent_id', $agent->id);
            }
        }
    }
}

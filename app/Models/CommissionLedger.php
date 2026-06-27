<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class CommissionLedger extends Model
{
    protected $table = 'commission_ledgers';

    protected $fillable = [
        'agent_id',
        'prospect_jemaah_id',
        'type', // credit, debit
        'amount',
        'balance_after',
        'status', // pending, approved, disbursed, rejected
        'disbursement_reference'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    /**
     * Apply multi-tenant global scope.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * Get the agent who owns the ledger entry.
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the prospect pilgrim who triggered this commission (if credit).
     */
    public function prospectJemaah()
    {
        return $this->belongsTo(ProspectJemaah::class, 'prospect_jemaah_id');
    }
}

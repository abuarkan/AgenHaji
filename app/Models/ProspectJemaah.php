<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class ProspectJemaah extends Model
{
    protected $table = 'prospect_jemaahs';

    protected $fillable = [
        'agent_id',
        'name',
        'nik',
        'address',
        'location_lat',
        'location_lng',
        'ktp_photo_path',
        'saving_book_photo_path',
        'npwp_photo_path',
        'email',
        'phone_number',
        'registration_type',
        'bank_id',
        'bps_bpih',
        'status_pendaftaran',
        'porsi_number',
        'registration_channel',
        'siskehat_sync_at',
        'siskehat_reference_id',
        'is_porsi_bound',
        'claim_status',
        'verified_at'
    ];

    protected $casts = [
        'location_lat' => 'decimal:8',
        'location_lng' => 'decimal:8',
        'verified_at' => 'datetime',
        'siskehat_sync_at' => 'datetime',
        'is_porsi_bound' => 'boolean',
    ];

    /**
     * Apply multi-tenant global scope.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * Get the Agent that registered this prospect.
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Accessor to get commission transfer status.
     * Checks if the commission credit ledger is disbursed or if a debit ledger exists for this prospect.
     *
     * @return string
     */
    public function getCommissionTransferStatusAttribute()
    {
        $credit = \App\Models\CommissionLedger::withoutGlobalScopes()
            ->where('prospect_jemaah_id', $this->id)
            ->where('type', 'credit')
            ->first();
            
        if ($credit && $credit->status === 'disbursed') {
            return 'Sudah Ditransfer';
        }

        $debit = \App\Models\CommissionLedger::withoutGlobalScopes()
            ->where('prospect_jemaah_id', $this->id)
            ->where('type', 'debit')
            ->whereIn('status', ['approved', 'disbursed'])
            ->exists();

        if ($debit) {
            return 'Sudah Ditransfer';
        }

        return 'Belum';
    }
}

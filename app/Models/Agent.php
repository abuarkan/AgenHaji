<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable = [
        'user_id',
        'institution_id',
        'agent_level_id',
        'referral_code',
        'nik',
        'whatsapp_number',
        'type',
        'status',
        'two_factor_secret',
        'two_factor_enabled',
        'is_email_verified',
        'is_whatsapp_verified',
        'is_ktp_verified',
        'email_verification_code',
        'email_verification_expires_at',
        'whatsapp_verification_code',
        'whatsapp_verification_expires_at',
        'full_name',
        'birth_date',
        'alamat_ktp',
        'provinsi_ktp',
        'kota_ktp',
        'kecamatan_ktp',
        'kelurahan_ktp',
        'alamat_tinggal',
        'provinsi_tinggal',
        'kota_tinggal',
        'kecamatan_tinggal',
        'kelurahan_tinggal',
        'foto_ktp',
        'foto_diri',
        'foto_bangunan',
        'foto_buku_tabungan',
        'nama_bank',
        'nomor_rekening',
        'cabang_bank',
        'nomor_npwp',
        'foto_npwp',
        'latitude_tinggal',
        'longitude_tinggal',
        'jenis_kelamin',
        'tempat_lahir',
        'foto_pakta_integritas',
        'is_submitted',
        'rejection_reason',
        'is_institution_admin',
        'bukti_pekerja',
        'sk_pengangkatan',
        'nip',
    ];

    /**
     * Apply multi-tenant global scope.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * Get the associated user account.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the associated institution (for B2B agents).
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Get the current level of the agent.
     */
    public function level()
    {
        return $this->belongsTo(AgentLevel::class, 'agent_level_id');
    }

    /**
     * Get all prospects registered by this agent.
     */
    public function prospects()
    {
        return $this->hasMany(ProspectJemaah::class);
    }

    /**
     * Get commission ledgers for this agent.
     */
    public function commissionLedgers()
    {
        return $this->hasMany(CommissionLedger::class);
    }

    /**
     * Get sub-agents referred by this agent.
     */
    public function subAgents()
    {
        return $this->belongsToMany(
            Agent::class,
            'agent_referrals',
            'parent_agent_id',
            'child_agent_id'
        )->withTimestamps();
    }

    /**
     * Get the parent agent who referred this agent.
     */
    public function parentAgent()
    {
        return $this->belongsToMany(
            Agent::class,
            'agent_referrals',
            'child_agent_id',
            'parent_agent_id'
        )->withTimestamps();
    }

    /**
     * Helper to get total validated commission balance.
     */
    public function getCommissionBalanceAttribute()
    {
        $credit = $this->commissionLedgers()
            ->where('status', 'approved')
            ->where('type', 'credit')
            ->sum('amount');

        $debit = $this->commissionLedgers()
            ->whereIn('status', ['approved', 'disbursed'])
            ->where('type', 'debit')
            ->sum('amount');

        return $credit - $debit;
    }
}

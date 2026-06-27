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
        'claim_status',
        'verified_at'
    ];

    protected $casts = [
        'location_lat' => 'decimal:8',
        'location_lng' => 'decimal:8',
        'verified_at' => 'datetime',
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentPointLedger extends Model
{
    protected $fillable = [
        'agent_id',
        'prospect_jemaah_id',
        'type',
        'points',
        'description',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function prospectJemaah()
    {
        return $this->belongsTo(ProspectJemaah::class);
    }
}

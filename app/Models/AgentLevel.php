<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentLevel extends Model
{
    protected $fillable = ['name', 'target_prospects', 'commission_per_prospect'];

    /**
     * Get all agents assigned to this level.
     */
    public function agents()
    {
        return $this->hasMany(Agent::class);
    }
}

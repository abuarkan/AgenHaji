<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $fillable = [
        'name',
        'registration_number',
        'address',
        'status',
        'legal_document',
        'npwp',
        'bank_account',
        'latitude',
        'longitude'
    ];

    /**
     * Get all agents belonging to the institution.
     */
    public function agents()
    {
        return $this->hasMany(Agent::class);
    }
}

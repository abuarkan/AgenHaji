<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralProgram extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Scope query to check if there is an active program on a given date.
     */
    public function scopeActiveOnDate($query, $date = null)
    {
        $date = $date ? \Carbon\Carbon::parse($date)->format('Y-m-d') : now()->format('Y-m-d');

        return $query->where('is_active', true)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date);
    }
}

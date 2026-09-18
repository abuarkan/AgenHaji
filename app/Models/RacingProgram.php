<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RacingProgram extends Model
{
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'announcement_date',
        'min_portion_target',
        'reward_type',
        'winner_quota',
        'target_bps_bpih',
        'prize_rank_1',
        'prize_rank_2',
        'prize_rank_3',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'announcement_date' => 'date',
        'is_active' => 'boolean',
        'winner_quota' => 'integer',
        'min_portion_target' => 'integer',
    ];

    /**
     * Scope query for active racing programs.
     */
    public function scopeActiveOnDate($query, $date = null)
    {
        $date = $date ? \Carbon\Carbon::parse($date)->format('Y-m-d') : now()->format('Y-m-d');

        return $query->where('is_active', true)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date);
    }
}

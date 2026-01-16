<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaPolicyRule extends Model
{
    protected $fillable = [
        'sla_policy_id',
        'priority',
        'response_time_minutes',
        'resolution_time_minutes',
    ];

    protected $casts = [
        'response_time_minutes' => 'integer',
        'resolution_time_minutes' => 'integer',
    ];

    public function slaPolicy(): BelongsTo
    {
        return $this->belongsTo(SlaPolicy::class);
    }

    /**
     * Get human-readable response time.
     */
    public function getResponseTimeHumanAttribute(): string
    {
        return $this->formatMinutes($this->response_time_minutes);
    }

    /**
     * Get human-readable resolution time.
     */
    public function getResolutionTimeHumanAttribute(): string
    {
        return $this->formatMinutes($this->resolution_time_minutes);
    }

    /**
     * Format minutes into human-readable string.
     */
    protected function formatMinutes(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} min".($minutes > 1 ? 's' : '');
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours < 24) {
            $result = "{$hours} hour".($hours > 1 ? 's' : '');
            if ($remainingMinutes > 0) {
                $result .= " {$remainingMinutes} min".($remainingMinutes > 1 ? 's' : '');
            }

            return $result;
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        $result = "{$days} day".($days > 1 ? 's' : '');
        if ($remainingHours > 0) {
            $result .= " {$remainingHours} hour".($remainingHours > 1 ? 's' : '');
        }

        return $result;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessHours extends Model
{
    protected $fillable = [
        'client_account_id',
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_closed' => 'boolean',
    ];

    public function clientAccount(): BelongsTo
    {
        return $this->belongsTo(ClientAccount::class);
    }

    /**
     * Day names for display.
     */
    public static array $dayNames = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    /**
     * Get the day name.
     */
    public function getDayNameAttribute(): string
    {
        return self::$dayNames[$this->day_of_week] ?? 'Unknown';
    }

    /**
     * Get business hours for a client account.
     */
    public static function getForClient(ClientAccount $clientAccount): array
    {
        $hours = static::where('client_account_id', $clientAccount->id)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        // Return as array indexed by day of week
        $result = [];
        for ($day = 0; $day <= 6; $day++) {
            $result[$day] = $hours->get($day);
        }

        return $result;
    }

    /**
     * Create default business hours (Mon-Fri 9-5) for a client.
     */
    public static function createDefaultForClient(ClientAccount $clientAccount): void
    {
        for ($day = 0; $day <= 6; $day++) {
            static::create([
                'client_account_id' => $clientAccount->id,
                'day_of_week' => $day,
                'open_time' => '09:00:00',
                'close_time' => '17:00:00',
                'is_closed' => in_array($day, [0, 6]), // Sunday & Saturday closed
            ]);
        }
    }
}

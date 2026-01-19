<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use App\Models\Concerns\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Event extends Model
{
    use BelongsToClient, HasFactory, HasHashid;

    protected $fillable = [
        'client_account_id',
        'created_by',
        'title',
        'description',
        'type',
        'meeting_link',
        'location',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            // Auto-generate Jitsi meeting link for virtual events
            if ($event->type === 'virtual' && empty($event->meeting_link)) {
                $event->meeting_link = static::generateMeetingLink();
            }
        });
    }

    /**
     * Generate a unique Jitsi meeting link.
     */
    public static function generateMeetingLink(): string
    {
        $roomCode = Str::lower(Str::random(12));

        return "https://meet.jit.si/optima-{$roomCode}";
    }

    /**
     * Get the attendees for this event.
     */
    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_attendees')
            ->withPivot(['status', 'responded_at'])
            ->withTimestamps();
    }

    /**
     * Get attendees who have accepted.
     */
    public function acceptedAttendees(): BelongsToMany
    {
        return $this->attendees()->wherePivot('status', 'accepted');
    }

    /**
     * Get attendees who have declined.
     */
    public function declinedAttendees(): BelongsToMany
    {
        return $this->attendees()->wherePivot('status', 'declined');
    }

    /**
     * Get attendees with pending status.
     */
    public function pendingAttendees(): BelongsToMany
    {
        return $this->attendees()->wherePivot('status', 'pending');
    }

    /**
     * Get the user who created this event.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if this is a virtual event.
     */
    public function isVirtual(): bool
    {
        return $this->type === 'virtual';
    }

    /**
     * Check if this is a physical event.
     */
    public function isPhysical(): bool
    {
        return $this->type === 'physical';
    }

    /**
     * Check if the event is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->starts_at->isFuture();
    }

    /**
     * Check if the event is past.
     */
    public function isPast(): bool
    {
        return $this->starts_at->isPast();
    }

    /**
     * Scope to filter upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', now())->orderBy('starts_at', 'asc');
    }

    /**
     * Scope to filter past events.
     */
    public function scopePast($query)
    {
        return $query->where('starts_at', '<=', now())->orderBy('starts_at', 'desc');
    }

    /**
     * Get the formatted date string.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->starts_at->format('l, F j, Y');
    }

    /**
     * Get the formatted time string.
     */
    public function getFormattedTimeAttribute(): string
    {
        $time = $this->starts_at->format('g:i A');

        if ($this->ends_at) {
            $time .= ' - '.$this->ends_at->format('g:i A');
        }

        return $time;
    }
}

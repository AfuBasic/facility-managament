<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationLog extends Model
{
    use BelongsToClient;

    protected $guarded = [];

    protected $casts = [
        'invited_at' => 'datetime',
        'accepted_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_new_user' => 'boolean',
    ];

    public function clientAccount(): BelongsTo
    {
        return $this->belongsTo(ClientAccount::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', ClientMembership::STATUS_PENDING);
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', ClientMembership::STATUS_ACCEPTED);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', ClientMembership::STATUS_EXPIRED);
    }
}

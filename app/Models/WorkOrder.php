<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    use BelongsToClient;
    protected $fillable = [
        'client_account_id',
        'facility_id',
        'space_id',
        'asset_id',
        'title',
        'description',
        'priority',
        'status',
        'reported_by',
        'reported_at',
        'approved_by',
        'approved_at',
        'approval_note',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'assignment_note',
        'started_by',
        'started_at',
        'paused_by',
        'paused_at',
        'pause_reason',
        'resumed_by',
        'resumed_at',
        'completed_by',
        'completed_at',
        'completion_notes',
        'time_spent',
        'total_cost',
        'closed_by',
        'closed_at',
        'closure_note',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'paused_at' => 'datetime',
        'resumed_at' => 'datetime',
        'completed_at' => 'datetime',
        'closed_at' => 'datetime',
        'total_cost' => 'decimal:2',
    ];

    // Core Relationships
    public function clientAccount(): BelongsTo
    {
        return $this->belongsTo(ClientAccount::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    // User Relationships
    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function startedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function pausedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paused_by');
    }

    public function resumedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resumed_by');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // History & Assets
    public function history(): HasMany
    {
        return $this->hasMany(WorkOrderHistory::class);
    }

    public function allocatedAssets(): HasMany
    {
        return $this->hasMany(WorkOrderAsset::class);
    }

    // Scopes
    public function scopeReported($query)
    {
        return $query->where('status', 'reported');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeOnHold($query)
    {
        return $query->where('status', 'on_hold');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    // State Validation Methods
    public function canApprove(): bool
    {
        return $this->status === 'reported';
    }

    public function canReject(): bool
    {
        return $this->status === 'reported';
    }

    public function canAssign(): bool
    {
        return $this->status === 'approved';
    }

    public function canStart(): bool
    {
        return $this->status === 'assigned';
    }

    public function canPause(): bool
    {
        return $this->status === 'in_progress';
    }

    public function canResume(): bool
    {
        return $this->status === 'on_hold';
    }

    public function canMarkDone(): bool
    {
        return $this->status === 'in_progress';
    }

    public function canApproveCompletion(): bool
    {
        return $this->status === 'completed';
    }

    public function canRejectCompletion(): bool
    {
        return $this->status === 'completed';
    }

    public function canClose(): bool
    {
        return $this->status === 'completed';
    }

    public function canReopen(): bool
    {
        return $this->status === 'completed';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function canReceiveUpdates(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCreator($user): bool
    {
        return $this->reported_by === $user->id;
    }

    public function isAssignee($user): bool
    {
        return $this->assigned_to === $user->id;
    }
}

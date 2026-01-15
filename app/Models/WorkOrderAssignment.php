<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderAssignment extends Model
{
    protected $fillable = [
        'work_order_id',
        'assigned_to',
        'assigned_by',
        'unassigned_by',
        'assigned_at',
        'unassigned_at',
        'assignment_note',
        'unassignment_reason',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'unassigned_at' => 'datetime',
            'is_current' => 'boolean',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function unassigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unassigned_by');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopePast($query)
    {
        return $query->where('is_current', false);
    }
}

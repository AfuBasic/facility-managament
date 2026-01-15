<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderAsset extends Model
{
    protected $fillable = [
        'work_order_id',
        'asset_id',
        'action',
        'quantity',
        'user_id',
        'performed_at',
        'note',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'quantity' => 'integer',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

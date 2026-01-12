<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetHistory extends Model
{
    use HasFactory;

    protected $table = 'asset_history';

    protected $fillable = [
        'asset_id',
        'action_type', // Replaces status for detailed tracking
        'performed_by_user_id',
        'target_user_id',
        'cost_per_unit',
        'previous_state',
        'status', // Keeping for backward compatibility if needed, or mapping to action_type
        'user_id', // Deprecated, use performed_by_user_id
        'receiver_id', // Deprecated, use target_user_id
        'units',
        'note',
        'to_store',
    ];

    protected $casts = [
        'status' => 'string',
        'previous_state' => 'array',
        'cost_per_unit' => 'decimal:2',
    ];

    /**
     * Get the asset that owns the history record
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the user who performed the action
     */
    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }

    /**
     * Get the target user (receiver)
     */
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /**
     * Get the destination store
     */
    public function toStore()
    {
        return $this->belongsTo(Store::class, 'to_store');
    }
}

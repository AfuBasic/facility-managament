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
        'action_type', // restock, checkout, checkin, audit, maintenance
        'performed_by_user_id',
        'target_user_id',
        'space_id', // Context location
        'cost_per_unit',
        'note',
        'previous_state',
    ];

    protected $casts = [
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

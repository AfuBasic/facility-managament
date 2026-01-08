<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class FacilityUser extends Pivot
{
    /**
     * The table associated with the model.
     */
    protected $table = 'facility_users';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'facility_id',
        'user_id',
        'client_account_id',
        'designation',
        'assigned_at',
        'removed_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'assigned_at' => 'datetime',
        'removed_at' => 'datetime',
    ];

    /**
     * Get the facility that this pivot belongs to
     */
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Get the user that this pivot belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only active assignments
     */
    public function scopeActive($query)
    {
        return $query->whereNull('removed_at');
    }

    /**
     * Scope to get only dormant assignments
     */
    public function scopeDormant($query)
    {
        return $query->whereNotNull('removed_at');
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Facility extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'address',
        'contact_person_name',
        'contact_person_phone',
        'client_account_id',
    ];

    /**
     * Get the client account that owns the facility
     */
    public function clientAccount(): BelongsTo
    {
        return $this->belongsTo(ClientAccount::class);
    }

    /**
     * Get the users assigned to this facility
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'facility_user')
            ->withPivot('assigned_at', 'removed_at')
            ->withTimestamps()
            ->whereNull('facility_user.removed_at');
    }

    /**
     * Get all users including removed ones
     */
    public function allUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'facility_user')
            ->withPivot('assigned_at', 'removed_at')
            ->withTimestamps();
    }

    /**
     * Get the spaces in this facility
     */
    public function spaces(): HasMany
    {
        return $this->hasMany(Space::class);
    }

    /**
     * Scope to filter facilities for a specific client
     */
    public function scopeForClient(Builder $query, int $clientAccountId): Builder
    {
        return $query->where('client_account_id', $clientAccountId);
    }

    /**
     * Scope to filter facilities assigned to a specific user
     */
    public function scopeAssignedToUser(Builder $query, int $userId): Builder
    {
        return $query->whereHas('users', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Assign a user to this facility
     */
    public function assignUser(int $userId): void
    {
        // Check if user was previously assigned and removed
        $existing = $this->allUsers()->where('user_id', $userId)->first();
        
        if ($existing && $existing->pivot->removed_at) {
            // Reactivate the assignment
            $this->allUsers()->updateExistingPivot($userId, [
                'removed_at' => null,
                'assigned_at' => now(),
            ]);
        } else {
            // New assignment
            $this->users()->attach($userId, [
                'assigned_at' => now(),
            ]);
        }
    }

    /**
     * Remove a user from this facility
     */
    public function removeUser(int $userId): void
    {
        $this->allUsers()->updateExistingPivot($userId, [
            'removed_at' => now(),
        ]);
    }

    /**
     * Check if a user is assigned to this facility
     */
    public function isAssignedToUser(int $userId): bool
    {
        return $this->users()->where('user_id', $userId)->exists();
    }
}

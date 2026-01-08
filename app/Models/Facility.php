<?php
namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\ClientAccount;
use App\Models\Space;
use App\Models\FacilityUser;

class Facility extends Model
{
    use HasFactory, BelongsToClient;
    
    protected $fillable = [
        'name',
        'address',
        'client_account_id',
    ];

    /**
     * Get the contact person (first assigned user) for this facility
     */
    public function getContactPerson(): ?User
    {
        return $this->users->first();
    }

    /**
     * Get contact person name from first assigned user
     */
    public function getContactPersonNameAttribute(): string
    {
        $contactPerson = $this->getContactPerson();
        
        if (!$contactPerson) {
            return 'Unassigned';
        }
        
        return $contactPerson->name ?? 'New User';
    }

    /**
     * Get contact person phone/email from first assigned user
     */
    public function getContactPersonPhoneAttribute(): ?string
    {
        $contactPerson = $this->getContactPerson();
        
        if (!$contactPerson) {
            return null;
        }
        
        return $contactPerson->phone ?? $contactPerson->email ?? null;
    }

    /**
     * Get the client account that owns the facility
     */
    public function clientAccount(): BelongsTo
    {
        return $this->belongsTo(ClientAccount::class);
    }

    /**
     * Get the users (managers) assigned to this facility (active only)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'facility_users')
            ->using(FacilityUser::class)
            ->withPivot('designation', 'assigned_at', 'removed_at', 'client_account_id')
            ->withTimestamps()
            ->whereNull('facility_users.removed_at');
    }

    /**
     * Get all users (managers) including dormant ones
     */
    public function allUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'facility_users')
            ->using(FacilityUser::class)
            ->withPivot('designation', 'assigned_at', 'removed_at', 'client_account_id')
            ->withTimestamps();
    }

    /**
     * Get dormant users (managers) who have been unassigned
     */
    public function dormantUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'facility_users')
            ->using(FacilityUser::class)
            ->withPivot('designation', 'assigned_at', 'removed_at', 'client_account_id')
            ->withTimestamps()
            ->whereNotNull('facility_users.removed_at');
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

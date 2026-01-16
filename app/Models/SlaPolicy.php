<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SlaPolicy extends Model
{
    protected $fillable = [
        'client_account_id',
        'name',
        'description',
        'is_default',
        'business_hours_only',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'business_hours_only' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function clientAccount(): BelongsTo
    {
        return $this->belongsTo(ClientAccount::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(SlaPolicyRule::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    /**
     * Get the rule for a specific priority level.
     */
    public function getRuleForPriority(string $priority): ?SlaPolicyRule
    {
        return $this->rules()->where('priority', $priority)->first();
    }

    /**
     * Scope to only active policies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get default policy.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get the default SLA policy for a client account.
     */
    public static function getDefaultForClient(ClientAccount $clientAccount): ?self
    {
        return static::where('client_account_id', $clientAccount->id)
            ->active()
            ->default()
            ->first();
    }
}

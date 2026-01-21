<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'notification_email',
        'phone',
        'address',
        'currency',
    ];

    public function memberships(): HasMany
    {
        return $this->hasMany(ClientMembership::class);
    }

    public function slaPolicies(): HasMany
    {
        return $this->hasMany(SlaPolicy::class);
    }

    public function businessHours(): HasMany
    {
        return $this->hasMany(BusinessHours::class);
    }

    /**
     * Get the notification email, or null if not set.
     */
    public function getNotificationEmailAttribute(?string $value): ?string
    {
        return $value ?: null;
    }

    /**
     * Get the currency symbol for this client account.
     */
    public function getCurrencySymbol(): string
    {
        return $this->currency ?? '$';
    }

    /**
     * Format a monetary value with the client's currency symbol.
     */
    public function formatCurrency(float|int|string|null $amount, int $decimals = 2): string
    {
        $amount = (float) ($amount ?? 0);

        return $this->getCurrencySymbol().number_format($amount, $decimals);
    }
}

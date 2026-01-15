<?php

namespace App\Models\Concerns;

use App\Models\ClientAccount;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToClient
{
    /**
     * Boot the trait and register the creating event
     */
    protected static function bootBelongsToClient(): void
    {
        static::creating(function ($model) {
            // Only set client_account_id if it's not already set
            if (empty($model->client_account_id)) {
                // Try to get from container first
                try {
                    $clientAccount = app(ClientAccount::class);
                    if ($clientAccount && $clientAccount->id) {
                        $model->client_account_id = $clientAccount->id;

                        return;
                    }
                } catch (\Exception $e) {
                    // Container binding not available
                }

                // Fallback: Try to get from session
                $clientAccountId = session('current_client_account_id');
                if ($clientAccountId) {
                    $model->client_account_id = $clientAccountId;
                }
            }
        });
    }

    /**
     * Get the client account that owns this model
     */
    public function clientAccount(): BelongsTo
    {
        return $this->belongsTo(ClientAccount::class);
    }
}

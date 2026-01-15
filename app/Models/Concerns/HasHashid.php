<?php

namespace App\Models\Concerns;

use App\Services\HashidService;

/**
 * Trait for models that use Hashids for URL obfuscation
 *
 * TO REVERSE THIS IMPLEMENTATION:
 * 1. Remove this trait from models
 * 2. Remove getRouteKey() and resolveRouteBinding() methods
 * 3. Use regular {model} route parameters instead of {modelHash}
 */
trait HasHashid
{
    /**
     * Get the value of the model's route key (returns hashid instead of ID)
     */
    public function getRouteKey()
    {
        return app(HashidService::class)->encode($this->getKey());
    }

    /**
     * Retrieve the model for a bound value (decode hashid to ID)
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $hashidService = app(HashidService::class);
        $id = $hashidService->decode($value);

        if ($id === null) {
            return null;
        }

        return $this->where($this->getRouteKeyName(), $id)->first();
    }

    /**
     * Get the hashid for this model
     */
    public function getHashidAttribute(): string
    {
        return app(HashidService::class)->encode($this->getKey());
    }
}

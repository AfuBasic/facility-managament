<?php

namespace App\Services;

use Hashids\Hashids;

/**
 * Service for encoding/decoding IDs using Hashids
 * 
 * This obfuscates sequential IDs in URLs for better security and aesthetics.
 * 
 * To reverse this implementation:
 * 1. Remove calls to HashidService::encode() and HashidService::decode()
 * 2. Use regular IDs instead
 * 3. Update route model binding back to {facility} instead of {facilityHash}
 */
class HashidService
{
    private Hashids $hashids;

    public function __construct()
    {
        // Salt should be unique per application - stored in .env
        $salt = config('app.hashid_salt', 'optimaFMApp');
        $minLength = 140; // Minimum length of generated hash
        
        $this->hashids = new Hashids($salt, $minLength);
    }

    /**
     * Encode an ID to a hash
     */
    public function encode(int $id): string
    {
        return $this->hashids->encode($id);
    }

    /**
     * Decode a hash back to an ID
     */
    public function decode(string $hash): ?int
    {
        $decoded = $this->hashids->decode($hash);
        return $decoded[0] ?? null;
    }

    /**
     * Encode multiple IDs
     */
    public function encodeMultiple(array $ids): string
    {
        return $this->hashids->encode(...$ids);
    }

    /**
     * Decode to multiple IDs
     */
    public function decodeMultiple(string $hash): array
    {
        return $this->hashids->decode($hash);
    }
}

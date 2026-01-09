<?php

namespace App\Actions\Client\Facilities;

use App\Models\Facility;

class CreateFacility
{
    /**
     * Create a new facility
     */
    public function execute(
        string $name,
        ?string $address,
        int $clientAccountId
    ): Facility {
        return Facility::create([
            'name' => $name,
            'address' => $address,
        ]);
    }
}

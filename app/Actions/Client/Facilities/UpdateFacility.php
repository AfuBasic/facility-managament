<?php

namespace App\Actions\Client\Facilities;

use App\Models\Facility;

class UpdateFacility
{
    /**
     * Update an existing facility
     */
    public function execute(
        Facility $facility,
        string $name,
        ?string $address
    ): Facility {
        $facility->update([
            'name' => $name,
            'address' => $address,
        ]);

        return $facility->fresh();
    }
}

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
        ?string $address,
        ?string $contactPersonName,
        ?string $contactPersonPhone
    ): Facility {
        $facility->update([
            'name' => $name,
            'address' => $address,
            'contact_person_name' => $contactPersonName,
            'contact_person_phone' => $contactPersonPhone,
        ]);

        return $facility->fresh();
    }
}

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
        ?string $contactPersonName,
        ?string $contactPersonPhone,
        int $clientAccountId
    ): Facility {
        return Facility::create([
            'name' => $name,
            'address' => $address,
            'contact_person_name' => $contactPersonName,
            'contact_person_phone' => $contactPersonPhone,
            'client_account_id' => $clientAccountId,
        ]);
    }
}

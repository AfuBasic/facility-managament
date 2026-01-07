<?php

namespace App\Actions\Client\Facilities;

use App\Models\Facility;

class RemoveUserFromFacility
{
    /**
     * Remove a user from a facility
     */
    public function execute(Facility $facility, int $userId): void
    {
        $facility->removeUser($userId);
    }
}

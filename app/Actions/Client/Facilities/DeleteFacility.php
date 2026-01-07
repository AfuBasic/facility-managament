<?php

namespace App\Actions\Client\Facilities;

use App\Models\Facility;

class DeleteFacility
{
    /**
     * Delete a facility
     */
    public function execute(Facility $facility): void
    {
        // TODO: Add checks for related records (spaces, assets, work orders)
        // For now, cascade delete will handle facility_user records
        
        $facility->delete();
    }
}

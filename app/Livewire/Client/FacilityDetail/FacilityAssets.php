<?php

namespace App\Livewire\Client\FacilityDetail;

use App\Models\Facility;
use Livewire\Component;

class FacilityAssets extends Component
{
    public Facility $facility;
    public function hydrate()
    {
        if($this->clientAccount) {
            setPermissionsTeamId($this->clientAccount->id);
        }
    }
    public function render()
    {
        return view('livewire.client.facility-detail.facility-assets');
    }
}

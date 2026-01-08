<?php

namespace App\Livewire\Client\FacilityDetail;

use App\Models\Facility;
use Livewire\Component;

class FacilityConsumables extends Component
{
    public Facility $facility;
    
    public function render()
    {
        return view('livewire.client.facility-detail.facility-consumables');
    }
}

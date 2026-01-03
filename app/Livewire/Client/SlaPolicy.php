<?php

namespace App\Livewire\Client;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
#[Title('SLA Policy | Optima FM')]
class SlaPolicy extends Component
{
    public function render()
    {
        return view('livewire.client.sla-policy');
    }
}

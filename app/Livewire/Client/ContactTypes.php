<?php

namespace App\Livewire\Client;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
#[Title('Contact Types | Optima FM')]
class ContactTypes extends Component
{
    public function render()
    {
        return view('livewire.client.contact-types');
    }
}

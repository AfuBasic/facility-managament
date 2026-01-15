<?php

namespace App\Livewire\Client;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
#[Title('Dashboard | Optima FM')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.client.dashboard.index');
    }
}

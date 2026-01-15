<?php

namespace App\Livewire\Client;

use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
#[Title('SLA Policy | Optima FM')]
class SlaPolicy extends Component
{
    public function mount()
    {
        Gate::authorize('view sla policy');
    }

    public function render()
    {
        return view('livewire.client.sla-policy.index');
    }
}

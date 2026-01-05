<?php

namespace App\Livewire\Client;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.client-app')]
#[Title('Work Orders | Optima FM')]
class WorkOrders extends Component
{
    public function mount()
    {
        Gate::authorize('view work orders');
    }

    public function render()
    {
        return view('livewire.client.work-orders.index');
    }
}

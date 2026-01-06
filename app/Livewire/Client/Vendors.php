<?php

namespace App\Livewire\Client;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.client-app')]
#[Title('Vendors | Optima FM')]
class Vendors extends Component
{
    public function mount()
    {
        Gate::authorize('view vendors');
    }

    public function render()
    {
        return view('livewire.client.vendors.index');
    }
}

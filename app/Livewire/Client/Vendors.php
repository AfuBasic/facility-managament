<?php

namespace App\Livewire\Client;

use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

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

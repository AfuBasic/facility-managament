<?php

namespace App\Livewire\Client;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.client-app')]
#[Title('Assets | Optima FM')]
class Assets extends Component
{
    public function mount()
    {
        Gate::authorize('view assets');
    }

    public function render()
    {
        return view('livewire.client.assets.index');
    }
}

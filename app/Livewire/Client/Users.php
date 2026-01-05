<?php

namespace App\Livewire\Client;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.client-app')]
#[Title('Users | Optima FM')]
class Users extends Component
{
    public function mount()
    {
        Gate::authorize('view users');
    }

    public function render()
    {
        return view('livewire.client.users.index');
    }
}

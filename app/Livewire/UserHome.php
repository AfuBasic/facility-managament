<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Clients | Optima FM')]
class UserHome extends Component
{
    public function render()
    {
        return view('livewire.user-home');
    }
}

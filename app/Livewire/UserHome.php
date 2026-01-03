<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.dashboard')]
#[Title('My Organizations | Optima FM')]
class UserHome extends Component
{
    public function render()
    {
        $memberships = Auth::user()->clientMemberships()->with('clientAccount')->get();

        return view('livewire.user-home', [
            'memberships' => $memberships
        ]);
    }
}

<?php

namespace App\Livewire;

use App\Models\ClientMembership;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('My Organizations | Optima FM')]
class UserHome extends Component
{
    public function render()
    {
        $memberships = Auth::user()->clientMemberships()->with('clientAccount')->get();

        $pendingInvitationsCount = ClientMembership::where('user_id', auth()->id())
            ->where('status', ClientMembership::STATUS_PENDING)
            ->count();

        return view('livewire.user-home', [
            'memberships' => $memberships,
            'pendingInvitationsCount' => $pendingInvitationsCount,
        ]);
    }
}

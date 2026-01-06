<?php

namespace App\Livewire;

use App\Models\ClientMembership;
use App\Services\InvitationTracker;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('My Invitations | Optima FM')]
class UserInvitations extends Component
{
    public function render()
    {
        $invitations = ClientMembership::where('user_id', auth()->id())
            ->whereIn('status', [ClientMembership::STATUS_PENDING, ClientMembership::STATUS_EXPIRED])
            ->with('clientAccount')
            ->latest()
            ->get();

        return view('livewire.user-invitations.index', [
            'invitations' => $invitations
        ]);
    }

    public function accept($membershipId)
    {
        $membership = ClientMembership::where('id', $membershipId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $membership->status = ClientMembership::STATUS_ACCEPTED;
        $membership->save();
        
        // Track acceptance
        app(InvitationTracker::class)->recordAcceptance(
            email: $membership->user->email,
            clientAccountId: $membership->client_account_id,
            userId: auth()->id()
        );
        
        $this->dispatch('toast', message: 'Invitation accepted successfully.', type: 'success');
    }

}

<?php

namespace App\Livewire;

use App\Models\ClientMembership;
use App\Models\InvitationLog;
use App\Services\InvitationTracker;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('My Invitations | Optima FM')]
class UserInvitations extends Component
{
    public $activeTab = 'pending';

    public function render()
    {
        $invitations = ClientMembership::where('user_id', auth()->id())
            ->whereIn('status', [ClientMembership::STATUS_PENDING, ClientMembership::STATUS_EXPIRED])
            ->with('clientAccount')
            ->latest()
            ->get();

        $invitationHistory = InvitationLog::where('email', auth()->user()->email)
            ->with(['clientAccount', 'invitedBy'])
            ->latest('invited_at')
            ->get();

        return view('livewire.user-invitations.index', [
            'invitations' => $invitations,
            'invitationHistory' => $invitationHistory,
        ]);
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
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

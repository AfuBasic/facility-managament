<?php

namespace App\Livewire\Client;

use Livewire\WithPagination;
use App\Actions\Client\Users\InviteUser;
use App\Actions\Client\Users\ResendInvitation;
use App\Actions\Client\Users\ResetUserAccount;
use App\Models\ClientAccount;
use App\Models\ClientMembership;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.client-app')]
#[Title('Users | Optima FM')]
class Users extends Component
{
    use WithPagination;

    public $email = '';
    public $role = '';
    public $showInviteModal = false;

    public function mount()
    {
        Gate::authorize('view users');
    }

    public function invite(InviteUser $inviteUser)
    {
        $this->validate([
            'email' => 'required|email',
            'role' => 'required|exists:roles,name'
        ]);
        
        $inviteUser->execute($this->email, $this->role, app(ClientAccount::class)->id);

        $this->showInviteModal = false;
        $this->reset(['email', 'role']);
        $this->dispatch('toast', message: 'Invitation sent successfully!', type: 'success');
    }

    public function resend($membershipId, ResendInvitation $resendInvitation)
    {
        $membership = ClientMembership::findOrFail($membershipId);
        $this->authorize('update', $membership->user);
        
        $resendInvitation->execute($membership);
        
        $this->dispatch('toast', message: 'Invitation resent successfully.', type: 'success');
    }

    public function resetAccount($membershipId, ResetUserAccount $resetAccount)
    {
        $membership = ClientMembership::findOrFail($membershipId);
        $this->authorize('update', $membership->user);
        
        $resetAccount->execute($membership);
        
        $this->dispatch('toast', message: 'Account reset and invitation sent.', type: 'success');
    }

    public function delete($membershipId)
    {
         $membership = ClientMembership::findOrFail($membershipId);
         $this->authorize('delete', $membership->user);
         
         $membership->delete();
         
         $this->dispatch('toast', message: 'User access removed.', type: 'success');
    }

    public function render()
    {
        $client = app(ClientAccount::class);
        
        $memberships = ClientMembership::where('client_account_id', $client->id)
            ->with(['user', 'user.roles'])
            ->whereHas('user.roles', function($q) {
                $q->where('name', '!=', 'admin');
            })
            ->latest()
            ->paginate(10);
            
        $roles = Role::where('client_account_id', $client->id)
            ->where('name', '!=', 'admin')
            ->get();

        return view('livewire.client.users.index', [
            'memberships' => $memberships,
            'roles' => $roles
        ]);
    }
}

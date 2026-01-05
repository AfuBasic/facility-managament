<?php

namespace App\Livewire\Client;

use Livewire\WithPagination;
use App\Actions\Client\Users\InviteUser;
use App\Actions\Client\Users\ResendInvitation;
use App\Actions\Client\Users\ResetUserAccount;
use App\Models\ClientAccount;
use App\Models\ClientMembership;
use App\Models\User;
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
    public ClientAccount | null $clientAccount;
    
    public function hydrate()
    {
        if ($this->clientAccount) {
            setPermissionsTeamId($this->clientAccount->id);
        }
    }
    
    public function mount()
    {
        $this->clientAccount = app(ClientAccount::class);
        $this->authorize('view users');
    }

    public function invite(InviteUser $inviteUser)
    {
        $this->validate([
            'email' => 'required|email',
            'role' => 'required|exists:roles,name'
        ]);

        $existingUser = User::where('email', $this->email)->first();
        if ($existingUser && ClientMembership::where('user_id', $existingUser->id)
            ->where('client_account_id', $this->clientAccount->id)
            ->exists()) {
            $this->addError('email', 'This user is already a member of this organization.');
            return;
        }
        
        $inviteUser->execute($this->email, $this->role, $this->clientAccount->id);

        $this->showInviteModal = false;
        $this->reset(['email', 'role']);
        $this->dispatch('toast', message: 'Invitation sent successfully!', type: 'success');
    }

    public function resend($membershipId, ResendInvitation $resendInvitation)
    {
        $membership = ClientMembership::findOrFail($membershipId);
        $this->authorize('edit users');
        
        $resendInvitation->execute($membership);
        
        $this->dispatch('toast', message: 'Invitation resent successfully.', type: 'success');
    }

    public function resetAccount($membershipId, ResetUserAccount $resetAccount)
    {
        $membership = ClientMembership::findOrFail($membershipId);
        $this->authorize('edit users');
        
        $resetAccount->execute($membership);
        
        $this->dispatch('toast', message: 'Account reset and invitation sent.', type: 'success');
    }

    public function delete($membershipId)
    {
         $membership = ClientMembership::findOrFail($membershipId);
         $this->authorize('delete users');
         
         $membership->delete();
         
         $this->dispatch('toast', message: 'User access removed.', type: 'success');
    }

    public function render()
    {
        $client = $this->clientAccount;
        
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

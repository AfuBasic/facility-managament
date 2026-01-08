<?php

namespace App\Livewire\Client;

use Livewire\WithPagination;
use App\Actions\Client\Users\InviteUser;
use App\Actions\Client\Users\ResendInvitation;
use App\Actions\Client\Users\ResetUserAccount;
use App\Actions\Client\Users\UpdateUserRole;
use App\Livewire\Concerns\WithNotifications;
use App\Models\ClientAccount;
use App\Models\ClientMembership;
use App\Models\User;
use App\Repositories\MembershipRepository;
use App\Repositories\RoleRepository;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.client-app')]
#[Title('Users | Optima FM')]
class Users extends Component
{
    use WithPagination, WithNotifications;

    public $email = '';
    public $role = '';
    public $showInviteModal = false;
    public $showEditRoleModal = false;
    public $editingMembershipId = null;
    public $selectedRole = '';
    public $search = '';
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
        
        // Auto-open invite modal if invite parameter is present
        if (request()->query('invite') === 'true') {
            $this->openInviteModal();
        }
    }

    public function openInviteModal()
    {
        $this->authorize('create users');
        $this->showInviteModal = true;
    }

    public function invite(InviteUser $inviteUser)
    {
        $this->authorize('create users');
        
        $this->validate([
            'email' => 'required|email',
            'role' => 'required|exists:roles,name'
        ]);

        if ($this->isExistingMember()) {
            $this->addError('email', 'This user is already a member of this organization.');
            return;
        }
        
        $inviteUser->execute($this->email, $this->role, $this->clientAccount->id);

        $this->closeModal('showInviteModal', ['email', 'role']);
        $this->success('Invitation sent successfully!');
    }

    public function resend($membershipId, ResendInvitation $resendInvitation)
    {
        $this->authorize('edit users');
        
        $membership = $this->membershipRepo()->findById($membershipId);
        $resendInvitation->execute($membership);
        
        $this->success('Invitation resent successfully.');
    }

    public function resetAccount($membershipId, ResetUserAccount $resetAccount)
    {
        $this->authorize('edit users');
        
        $membership = $this->membershipRepo()->findById($membershipId);
        $resetAccount->execute($membership);
        
        $this->success('Account reset and invitation sent.');
    }

    public function editRole($membershipId)
    {
        $this->authorize('edit users');
        
        $membership = ClientMembership::with('user.roles')->findOrFail($membershipId);
        $this->editingMembershipId = $membershipId;
        $this->selectedRole = $membership->user->roles()->first()?->name ?? '';
        $this->showEditRoleModal = true;
    }

    public function updateRole(UpdateUserRole $updateUserRole)
    {
        $this->authorize('edit users');
        
        $this->validate(['selectedRole' => 'required|exists:roles,name']);

        $membership = $this->membershipRepo()->findById($this->editingMembershipId);
        $updateUserRole->execute($membership, $this->selectedRole);

        $this->closeModal('showEditRoleModal');
        $this->success('User role updated successfully.');
    }

    public function delete($membershipId)
    {
        $this->authorize('delete users');
        
        $membership = $this->membershipRepo()->findById($membershipId);
        $membership->delete();
        
        $this->success('User access removed.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.client.users.index', [
            'memberships' => $this->membershipRepo()->getPaginatedForClient(
                $this->clientAccount->id,
                $this->search
            ),
            'roles' => $this->roleRepo()->getAllForClient($this->clientAccount->id)
        ]);
    }

    /**
     * Get MembershipRepository instance
     */
    private function membershipRepo(): MembershipRepository
    {
        return app(MembershipRepository::class);
    }

    /**
     * Get RoleRepository instance
     */
    private function roleRepo(): RoleRepository
    {
        return app(RoleRepository::class);
    }

    /**
     * Check if the email belongs to an existing member
     */
    private function isExistingMember(): bool
    {
        $existingUser = User::where('email', $this->email)->first();
        
        return $existingUser && $this->membershipRepo()->isUserMember(
            $existingUser->id,
            $this->clientAccount->id
        );
    }

    /**
     * Close a modal and optionally reset properties
     */
    private function closeModal(string $modalProperty, array $resetProperties = []): void
    {
        $this->$modalProperty = false;
        
        if (!empty($resetProperties)) {
            $this->reset($resetProperties);
        }
    }
}

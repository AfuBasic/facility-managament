<?php

namespace App\Livewire;

use App\Models\ClientAccount;
use App\Models\ClientMembership;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.dashboard')]
#[Title('My Organizations | Optima FM')]
class UserHome extends Component
{
    // Create Organization Modal
    public bool $showCreateModal = false;

    public string $orgName = '';

    public string $orgEmail = '';

    public string $orgPhone = '';

    public string $orgAddress = '';

    protected function rules(): array
    {
        return [
            'orgName' => 'required|string|min:2|max:255',
            'orgEmail' => 'nullable|email|max:255',
            'orgPhone' => 'nullable|string|max:20',
            'orgAddress' => 'nullable|string|max:500',
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function resetCreateForm(): void
    {
        $this->orgName = '';
        $this->orgEmail = '';
        $this->orgPhone = '';
        $this->orgAddress = '';
        $this->resetValidation();
    }

    public function createOrganization(): void
    {
        $this->validate();

        DB::transaction(function () {
            // Create the organization
            $clientAccount = ClientAccount::create([
                'name' => $this->orgName,
                'notification_email' => $this->orgEmail ?: null,
                'phone' => $this->orgPhone ?: null,
                'address' => $this->orgAddress ?: null,
            ]);

            // Add the creator as the first member (making them the owner)
            $clientAccount->memberships()->create([
                'user_id' => Auth::id(),
                'status' => ClientMembership::STATUS_ACCEPTED,
            ]);

            // Assign admin role to creator in this organization's context
            setPermissionsTeamId($clientAccount->id);
            $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'client_account_id' => $clientAccount->id]);
            Auth::user()->assignRole($role);
        });

        $this->showCreateModal = false;
        $this->resetCreateForm();

        session()->flash('success', 'Organization created successfully! You are now the admin.');
    }

    public function render()
    {
        $memberships = Auth::user()->clientMemberships()->with('clientAccount')->get();

        $pendingInvitationsCount = ClientMembership::where('user_id', Auth::id())
            ->where('status', ClientMembership::STATUS_PENDING)
            ->count();

        return view('livewire.user-home', [
            'memberships' => $memberships,
            'pendingInvitationsCount' => $pendingInvitationsCount,
        ]);
    }
}

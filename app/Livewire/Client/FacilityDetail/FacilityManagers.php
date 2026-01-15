<?php

namespace App\Livewire\Client\FacilityDetail;

use App\Livewire\Concerns\WithNotifications;
use App\Mail\ManagerAssignedToFacility;
use App\Models\ClientAccount;
use App\Models\ClientMembership;
use App\Models\Facility;
use App\Models\FacilityUser;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class FacilityManagers extends Component
{
    use WithNotifications;

    public Facility $facility;

    public ClientAccount $clientAccount;

    // Manager form fields
    public $showManagerModal = false;

    public $isEditingManager = false;

    public $editingManagerId = null;

    public $selectedUserId = null;

    public $managerDesignation = '';

    public $managerSearch = '';

    public $dormantManagerSearch = '';

    public function hydrate()
    {
        if ($this->clientAccount) {
            setPermissionsTeamId($this->clientAccount->id);
        }
    }

    public function getAvailableUsersProperty()
    {
        // Get IDs of users already assigned to this facility (active only)
        $assignedUserIds = FacilityUser::where('facility_id', $this->facility->id)
            ->whereNull('removed_at')
            ->pluck('user_id');

        // Return users from this client who are not assigned to this facility
        return ClientMembership::with(['user', 'user.roles'])->whereHas('user.roles', function ($query) {
            $query->where('name', '!=', 'admin');
        })
            ->whereNotIn('user_id', $assignedUserIds)
            ->get();
    }

    public function getFilteredActiveManagersProperty()
    {
        $query = $this->facility->users();

        if ($this->managerSearch) {
            $query->where(function ($q) {
                $q->where('users.name', 'like', "%{$this->managerSearch}%")
                    ->orWhere('users.email', 'like', "%{$this->managerSearch}%")
                    ->orWhere('facility_users.designation', 'like', "%{$this->managerSearch}%");
            });
        }

        return $query->get();
    }

    public function getFilteredDormantManagersProperty()
    {
        $query = $this->facility->dormantUsers();

        if ($this->dormantManagerSearch) {
            $query->where(function ($q) {
                $q->where('users.name', 'like', "%{$this->dormantManagerSearch}%")
                    ->orWhere('users.email', 'like', "%{$this->dormantManagerSearch}%")
                    ->orWhere('facility_users.designation', 'like', "%{$this->dormantManagerSearch}%");
            });
        }

        return $query->get();
    }

    public function openManagerModal()
    {

        $this->authorize('assign facility_managers');
        $this->resetManagerForm();
        $this->showManagerModal = true;
    }

    public function editManager($userId)
    {
        $this->authorize('assign facility_managers');

        $manager = $this->facility->allUsers()->where('user_id', $userId)->first();

        if (! $manager) {
            $this->error('Manager not found.');

            return;
        }

        $this->selectedUserId = $userId;
        $this->managerDesignation = $manager->pivot->designation;
        $this->isEditingManager = true;
        $this->editingManagerId = $userId;
        $this->showManagerModal = true;
    }

    public function saveManager()
    {
        $this->validate([
            'selectedUserId' => 'required|exists:users,id',
            'managerDesignation' => 'required|string|max:100',
        ]);

        if ($this->isEditingManager) {
            $this->authorize('assign facility_managers');

            // Update designation
            $this->facility->users()->updateExistingPivot($this->selectedUserId, [
                'designation' => $this->managerDesignation,
            ]);

            $this->success('Manager designation updated successfully!');
        } else {
            $this->authorize('assign facility_managers');

            // Attach new manager
            $this->facility->users()->attach($this->selectedUserId, [
                'designation' => $this->managerDesignation,
                'client_account_id' => $this->clientAccount->id,
                'assigned_at' => now(),
            ]);

            // Send email notification to the assigned manager
            $manager = User::find($this->selectedUserId);

            if ($manager) {
                Mail::to($manager->email)->send(
                    new ManagerAssignedToFacility(
                        $manager,
                        $this->facility,
                        $this->clientAccount,
                        $this->managerDesignation
                    )
                );
            }

            $this->success('Manager assigned successfully!');
        }

        $this->closeManagerModal();
        $this->facility->refresh();
    }

    public function unassignManager($userId)
    {
        $this->authorize('unassign facility_managers');

        // Soft delete: set removed_at
        $this->facility->users()->updateExistingPivot($userId, [
            'removed_at' => now(),
        ]);

        $this->success('Manager unassigned successfully!');
        $this->facility->refresh();
    }

    public function reactivateManager($userId)
    {
        $this->authorize('assign facility_managers');

        // Clear removed_at
        $this->facility->allUsers()->updateExistingPivot($userId, [
            'removed_at' => null,
        ]);

        $this->success('Manager reactivated successfully!');
        $this->facility->refresh();
    }

    public function deleteManager($userId)
    {
        $this->authorize('delete facility_managers');

        // Hard delete: detach from pivot table
        $this->facility->allUsers()->detach($userId);

        $this->success('Manager deleted permanently!');
        $this->facility->refresh();
    }

    public function closeManagerModal()
    {
        $this->showManagerModal = false;
        $this->resetManagerForm();
    }

    private function resetManagerForm()
    {
        $this->selectedUserId = null;
        $this->managerDesignation = '';
        $this->isEditingManager = false;
        $this->editingManagerId = null;
    }

    public function render()
    {
        return view('livewire.client.facility-detail.facility-managers');
    }
}

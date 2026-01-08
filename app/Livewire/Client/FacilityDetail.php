<?php

namespace App\Livewire\Client;

use App\Livewire\Concerns\WithNotifications;
use App\Mail\ManagerAssignedToFacility;
use App\Models\ClientAccount;
use App\Models\ClientMembership;
use App\Models\Facility;
use App\Models\FacilityUser;
use App\Models\Space;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

#[Layout('components.layouts.client-app')]
#[Title('Facility Details | Optima FM')]
class FacilityDetail extends Component
{
    use WithNotifications;
    
    public Facility $facility;
    public ClientAccount $clientAccount;
    public $activeTab = 'spaces';
    
    // Space form fields
    public $showSpaceModal = false;
    public $isEditingSpace = false;
    public $editingSpaceId = null;
    public $spaceName = '';
    public $spaceType = '';
    public $spaceFloor = '';
    public $spaceDescription = '';
    public $spaceStatus = 'active';
    
    // View space modal
    public $showViewSpaceModal = false;
    public $viewingSpace = null;
    
    // Manager form fields
    public $showManagerModal = false;
    public $isEditingManager = false;
    public $editingManagerId = null;
    public $selectedUserId = null;
    public $managerDesignation = '';
    public $managerSearch = '';
    public $dormantManagerSearch = '';
    
    protected $rules = [
        'spaceName' => 'required|string|max:255',
        'spaceType' => 'nullable|string|max:255',
        'spaceFloor' => 'nullable|string|max:255',
        'spaceDescription' => 'nullable|string',
        'spaceStatus' => 'required|in:active,inactive,maintenance',
        'selectedUserId' => 'required|exists:users,id',
        'managerDesignation' => 'required|string|max:100',
    ];
    
    public function hydrate()
    {
        if($this->clientAccount) {
            setPermissionsTeamId($this->clientAccount->id);
        }
    }
    
    public function mount($facility)
    {
        
        $this->clientAccount = app(ClientAccount::class);
        $this->facility = Facility::query()
        ->where('client_account_id', $this->clientAccount->id)
        ->findOrFail($facility->id);
        
        // Get available tabs based on permissions
        $availableTabs = $this->getAvailableTabs();
        
        // Check for tab query parameter, otherwise use first available tab
        $requestedTab = request()->query('tab');
        if ($requestedTab && in_array($requestedTab, $availableTabs)) {
            $this->activeTab = $requestedTab;
        } else {
            $this->activeTab = !empty($availableTabs) ? $availableTabs[0] : 'spaces';
        }
    }
    
    public function setTab($tab)
    {
        $this->activeTab = $tab;
        
        // Update URL with tab parameter without page reload
        $this->dispatch('update-url', tab: $tab);
    }
    
    /**
    * Get list of tabs available to current user based on permissions
    */
    public function getAvailableTabs(): array
    {
        $tabs = [];
        
        if (Auth::user()->can('view spaces')) {
            $tabs[] = 'spaces';
        }
        
        if (Auth::user()->can('view assets')) {
            $tabs[] = 'assets';
        }
        
        if (Auth::user()->can('view consumables')) {
            $tabs[] = 'consumables';
        }
        
        if (Auth::user()->can('view facility_managers')) {
            $tabs[] = 'managers';
        }
        
        return $tabs;
    }
    
    // Space Management Methods
    public function createSpace()
    {
        $this->authorize('create spaces');
        $this->resetSpaceForm();
        $this->showSpaceModal = true;
    }
    
    public function editSpace($id)
    {
        $this->authorize('edit spaces');
        
        $space = Space::where('facility_id', $this->facility->id)->findOrFail($id);
        
        $this->editingSpaceId = $space->id;
        $this->spaceName = $space->name;
        $this->spaceType = $space->type ?? '';
        $this->spaceFloor = $space->floor ?? '';
        $this->spaceDescription = $space->description ?? '';
        $this->spaceStatus = $space->status;
        $this->isEditingSpace = true;
        $this->showSpaceModal = true;
    }
    
    public function saveSpace()
    {
        $this->validate();
        
        if ($this->isEditingSpace) {
            $this->authorize('edit spaces');
            $space = Space::where('facility_id', $this->facility->id)->findOrFail($this->editingSpaceId);
            $space->update([
                'name' => $this->spaceName,
                'type' => $this->spaceType,
                'floor' => $this->spaceFloor,
                'description' => $this->spaceDescription,
                'status' => $this->spaceStatus,
            ]);
            $this->success('Space updated successfully!');
        } else {
            $this->authorize('create spaces');
            Space::create([
                'facility_id' => $this->facility->id,
                'name' => $this->spaceName,
                'type' => $this->spaceType,
                'floor' => $this->spaceFloor,
                'description' => $this->spaceDescription,
                'status' => $this->spaceStatus,
            ]);
            $this->success('Space created successfully!');
        }
        
        $this->closeSpaceModal();
        $this->facility->load('spaces');
    }
    
    public function deleteSpace($id)
    {
        $this->authorize('delete spaces');
        
        $space = Space::where('facility_id', $this->facility->id)->findOrFail($id);
        $space->delete();
        
        $this->success('Space deleted successfully.');
        $this->facility->load('spaces');
    }
    
    public function viewSpace($id)
    {
        $this->viewingSpace = Space::where('facility_id', $this->facility->id)->findOrFail($id);
        $this->showViewSpaceModal = true;
    }
    
    public function closeViewSpaceModal()
    {
        $this->showViewSpaceModal = false;
        $this->viewingSpace = null;
    }
    
    // Manager Management Methods
    public function getAvailableUsersProperty()
    {
        // Get IDs of users already assigned to this facility (active only)
        $assignedUserIds = FacilityUser::where('facility_id', $this->facility->id)
        ->whereNull('removed_at')
        ->pluck('user_id');
        // Return users from this client who are not assigned to this facility
        return ClientMembership::with(['user','user.roles'])->whereHas('user.roles', function ($query) {
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
        
        if (!$manager) {
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
            Mail::to($manager->email)->send(
                new ManagerAssignedToFacility(
                    $manager,
                    $this->facility,
                    $this->clientAccount,
                    $this->managerDesignation
                )
            );
            
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
    
    private function resetManagerForm()
    {
        $this->selectedUserId = null;
        $this->managerDesignation = '';
        $this->isEditingManager = false;
        $this->editingManagerId = null;
    }
    
    public function closeManagerModal()
    {
        $this->showManagerModal = false;
        $this->resetManagerForm();
    }
    
    public function render()
    {
        return view('livewire.client.facility-detail');
    }
    
    private function resetSpaceForm()
    {
        $this->spaceName = '';
        $this->spaceType = '';
        $this->spaceFloor = '';
        $this->spaceDescription = '';
        $this->spaceStatus = 'active';
        $this->isEditingSpace = false;
        $this->editingSpaceId = null;
    }
    
    public function closeSpaceModal()
    {
        $this->showSpaceModal = false;
        $this->resetSpaceForm();
    }
}

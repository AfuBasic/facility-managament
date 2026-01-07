<?php

namespace App\Livewire\Client;

use App\Livewire\Concerns\WithNotifications;
use App\Models\ClientAccount;
use App\Models\Facility;
use App\Models\Space;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

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
    public $spaceArea = '';
    public $spaceCapacity = '';
    public $spaceDescription = '';
    public $spaceStatus = 'active';

    protected $rules = [
        'spaceName' => 'required|string|max:255',
        'spaceType' => 'nullable|string|max:255',
        'spaceFloor' => 'nullable|string|max:255',
        'spaceArea' => 'nullable|numeric|min:0',
        'spaceCapacity' => 'nullable|integer|min:0',
        'spaceDescription' => 'nullable|string',
        'spaceStatus' => 'required|in:active,inactive,maintenance',
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
        $this->facility = Facility::with(['spaces', 'users'])
            ->where('client_account_id', $this->clientAccount->id)
            ->findOrFail($facility->id);
        
        $this->authorize('manage facilities');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    // Space Management Methods
    public function createSpace()
    {
        $this->authorize('manage facilities');
        $this->resetSpaceForm();
        $this->showSpaceModal = true;
    }

    public function editSpace($id)
    {
        $this->authorize('edit facilities');
        
        $space = Space::where('facility_id', $this->facility->id)->findOrFail($id);
        
        $this->editingSpaceId = $space->id;
        $this->spaceName = $space->name;
        $this->spaceType = $space->type ?? '';
        $this->spaceFloor = $space->floor ?? '';
        $this->spaceArea = $space->area ?? '';
        $this->spaceCapacity = $space->capacity ?? '';
        $this->spaceDescription = $space->description ?? '';
        $this->spaceStatus = $space->status;
        $this->isEditingSpace = true;
        $this->showSpaceModal = true;
    }

    public function saveSpace()
    {
        $this->validate();

        if ($this->isEditingSpace) {
            $this->authorize('edit facilities');
            $space = Space::where('facility_id', $this->facility->id)->findOrFail($this->editingSpaceId);
            $space->update([
                'name' => $this->spaceName,
                'type' => $this->spaceType,
                'floor' => $this->spaceFloor,
                'area' => $this->spaceArea,
                'capacity' => $this->spaceCapacity,
                'description' => $this->spaceDescription,
                'status' => $this->spaceStatus,
            ]);
            $this->success('Space updated successfully!');
        } else {
            $this->authorize('create facilities');
            Space::create([
                'facility_id' => $this->facility->id,
                'name' => $this->spaceName,
                'type' => $this->spaceType,
                'floor' => $this->spaceFloor,
                'area' => $this->spaceArea,
                'capacity' => $this->spaceCapacity,
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
        $this->authorize('delete facilities');
        
        $space = Space::where('facility_id', $this->facility->id)->findOrFail($id);
        $space->delete();
        
        $this->success('Space deleted successfully.');
        $this->facility->load('spaces');
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
        $this->spaceArea = '';
        $this->spaceCapacity = '';
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

<?php

namespace App\Livewire\Client\FacilityDetail;

use App\Livewire\Concerns\WithNotifications;
use App\Models\ClientAccount;
use App\Models\Facility;
use App\Models\Space;
use Livewire\Component;

class FacilitySpaces extends Component
{
    use WithNotifications;
    
    public Facility $facility;
    
    public ClientAccount $clientAccount;
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
    
    protected $rules = [
        'spaceName' => 'required|string|max:255',
        'spaceType' => 'nullable|string|max:255',
        'spaceFloor' => 'nullable|string|max:255',
        'spaceDescription' => 'nullable|string',
        'spaceStatus' => 'required|in:active,inactive,maintenance',
    ];
    
    public function hydrate()
    {
        if ($this->clientAccount) {
            setPermissionsTeamId($this->clientAccount->id);
        }
    }

    public function mount(){
        if (!$this->clientAccount) {
            $this->clientAccount = app(ClientAccount::class);
        }
        setPermissionsTeamId($this->clientAccount->id);
    }
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
    
    public function closeSpaceModal()
    {
        $this->showSpaceModal = false;
        $this->resetSpaceForm();
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
    
    public function render()
    {
        return view('livewire.client.facility-detail.facility-spaces');
    }
}

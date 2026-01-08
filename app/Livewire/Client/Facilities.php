<?php

namespace App\Livewire\Client;

use App\Actions\Client\Facilities\CreateFacility;
use App\Actions\Client\Facilities\DeleteFacility;
use App\Actions\Client\Facilities\UpdateFacility;
use App\Livewire\Concerns\WithNotifications;
use App\Models\ClientAccount;
use App\Repositories\FacilityRepository;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.client-app')]
#[Title('Facilities | Optima FM')]
class Facilities extends Component
{
    use WithPagination, WithNotifications;

    // Search
    public $search = '';

    // Modal state
    public $showModal = false;
    public $isEditing = false;
    public $editingFacilityId = null;

    // Form fields
    public $name = '';
    public $address = '';

    public ClientAccount $clientAccount;

    protected $rules = [
        'name' => 'required|string|max:255',
        'address' => 'nullable|string|max:500',
    ];

    public function hydrate()
    {
        if ($this->clientAccount) {
            setPermissionsTeamId($this->clientAccount->id);
        }
    }
    
    public function mount()
    {
        $this->authorize('view facilities');
        $this->clientAccount = app(ClientAccount::class);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->authorize('create facilities');
        
        $this->reset(['name', 'address', 'isEditing', 'editingFacilityId']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit facilities');
        
        $facility = $this->facilityRepo()->findForClient($id, $this->clientAccount->id);
        
        if (!$facility) {
            $this->error('Facility not found.');
            return;
        }

        $this->editingFacilityId = $facility->id;
        $this->name = $facility->name;
        $this->address = $facility->address ?? '';
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(CreateFacility $createFacility, UpdateFacility $updateFacility)
    {
        $this->validate();

        if ($this->isEditing) {
            $this->authorize('edit facilities');
            $facility = $this->facilityRepo()->findForClient($this->editingFacilityId, $this->clientAccount->id);
            $updateFacility->execute(
                $facility,
                $this->name,
                $this->address
            );
            $this->success('Facility updated successfully!');
        } else {
            $this->authorize('create facilities');
            $createFacility->execute(
                $this->name,
                $this->address,
                $this->clientAccount->id
            );
            $this->success('Facility created successfully!');
        }

        $this->closeModal();
    }

    public function delete($id, DeleteFacility $deleteFacility)
    {
        $this->authorize('delete facilities');
        
        $facility = $this->facilityRepo()->findForClient($id, $this->clientAccount->id);
        
        if (!$facility) {
            $this->error('Facility not found.');
            return;
        }

        $deleteFacility->execute($facility);
        $this->success('Facility deleted successfully.');
    }

    public function render()
    {
        $facilities = $this->facilityRepo()->getPaginatedForClient(
            $this->clientAccount->id,
            $this->search
        );

        return view('livewire.client.facilities.index', [
            'facilities' => $facilities
        ]);
    }

    private function facilityRepo(): FacilityRepository
    {
        return app(FacilityRepository::class);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['name', 'address', 'isEditing', 'editingFacilityId']);
    }
}

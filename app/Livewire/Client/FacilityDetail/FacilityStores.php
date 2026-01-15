<?php

namespace App\Livewire\Client\FacilityDetail;

use App\Livewire\Concerns\WithNotifications;
use App\Models\ClientAccount;
use App\Models\ClientMembership;
use App\Models\Facility;
use App\Models\Store;
use Livewire\Component;

class FacilityStores extends Component
{
    use WithNotifications;

    public Facility $facility;

    public ClientAccount $clientAccount;

    // Store form fields
    public $showStoreModal = false;

    public $isEditingStore = false;

    public $editingStoreId = null;

    public $storeName = '';

    public $storeManagerId = null;

    public $storeDescription = '';

    public $storeStatus = 'active';

    protected $rules = [
        'storeName' => 'required|string|max:255',
        'storeManagerId' => 'nullable|exists:users,id',
        'storeDescription' => 'nullable|string',
        'storeStatus' => 'required|in:active,inactive',
    ];

    public function hydrate()
    {
        if ($this->clientAccount) {
            setPermissionsTeamId($this->clientAccount->id);
        }
    }

    public function mount()
    {
        if (! $this->clientAccount) {
            $this->clientAccount = app(ClientAccount::class);
        }
        setPermissionsTeamId($this->clientAccount->id);

        // Check if we should auto-open edit modal
        if (request()->has('editStore')) {
            $storeId = Store::where('client_account_id', $this->clientAccount->id)
                ->get()
                ->firstWhere('hashid', request('editStore'))
                ?->id;

            if ($storeId) {
                $this->editStore($storeId);
            }
        }
    }

    public function getAvailableManagersProperty()
    {
        // Get users from this client who can be store managers
        return ClientMembership::with(['user', 'user.roles'])
            ->whereHas('user.roles', function ($query) {
                $query->where('name', '!=', 'admin');
            })
            ->get();
    }

    public function createStore()
    {
        $this->authorize('create stores');
        $this->resetStoreForm();
        $this->showStoreModal = true;
    }

    public function editStore($id)
    {
        $this->authorize('edit stores');

        $store = Store::where('facility_id', $this->facility->id)->findOrFail($id);

        $this->editingStoreId = $store->id;
        $this->storeName = $store->name;
        $this->storeManagerId = $store->store_manager_id;
        $this->storeDescription = $store->description ?? '';
        $this->storeStatus = $store->status;
        $this->isEditingStore = true;
        $this->showStoreModal = true;
    }

    public function saveStore()
    {
        $this->validate();

        if ($this->isEditingStore) {
            $this->authorize('edit stores');
            $store = Store::where('facility_id', $this->facility->id)->findOrFail($this->editingStoreId);
            $store->update([
                'name' => $this->storeName,
                'store_manager_id' => $this->storeManagerId,
                'description' => $this->storeDescription,
                'status' => $this->storeStatus,
            ]);
            $this->success('Store updated successfully!');
        } else {
            $this->authorize('create stores');
            Store::create([
                'facility_id' => $this->facility->id,
                'client_account_id' => $this->clientAccount->id,
                'name' => $this->storeName,
                'store_manager_id' => $this->storeManagerId,
                'description' => $this->storeDescription,
                'status' => $this->storeStatus,
            ]);
            $this->success('Store created successfully!');
        }

        $this->closeStoreModal();
        $this->facility->load('stores');
    }

    public function deleteStore($id)
    {
        $this->authorize('delete stores');

        $store = Store::where('facility_id', $this->facility->id)->findOrFail($id);
        $store->delete();

        $this->success('Store deleted successfully.');
        $this->facility->load('stores');
    }

    public function closeStoreModal()
    {
        $this->showStoreModal = false;
        $this->resetStoreForm();
    }

    private function resetStoreForm()
    {
        $this->storeName = '';
        $this->storeManagerId = null;
        $this->storeDescription = '';
        $this->storeStatus = 'active';
        $this->isEditingStore = false;
        $this->editingStoreId = null;
    }

    public function render()
    {
        return view('livewire.client.facility-detail.facility-stores');
    }
}

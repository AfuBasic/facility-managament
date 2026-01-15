<?php

namespace App\Livewire\Client;

use App\Models\ClientAccount;
use App\Models\Store;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
#[Title('Store Details | Optima FM')]
class StoreDetail extends Component
{
    public Store $store;

    public ClientAccount $clientAccount;

    #[Url]
    public $activeTab = 'overview';

    public function hydrate()
    {
        if ($this->clientAccount) {
            setPermissionsTeamId($this->clientAccount->id);
        }
    }

    public function mount(Store $store)
    {
        $this->clientAccount = app(ClientAccount::class);

        // Verify the store belongs to the current client
        if ($store->client_account_id !== $this->clientAccount->id) {
            abort(404);
        }

        $this->store = $store->load(['facility', 'storeManager']);
    }

    public function deleteStore()
    {
        $this->authorize('delete stores');

        $facilityHashid = $this->store->facility->hashid;
        $this->store->delete();

        return $this->redirect(route('app.facilities.show', $facilityHashid).'?tab=stores', navigate: true);
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.client.store-detail');
    }
}

<?php

namespace App\Livewire\Client;

use App\Models\ClientAccount;
use App\Models\Facility;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
#[Title('Facility Details | Optima FM')]
class FacilityDetail extends Component
{
    public Facility $facility;

    public ClientAccount $clientAccount;

    public $activeTab = 'spaces';

    public function hydrate()
    {
        if ($this->clientAccount) {
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
            $this->activeTab = ! empty($availableTabs) ? $availableTabs[0] : 'spaces';
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

        if (Auth::user()->can('view stores')) {
            $tabs[] = 'stores';
        }

        if (Auth::user()->can('view facility_managers')) {
            $tabs[] = 'managers';
        }

        return $tabs;
    }

    public function render()
    {
        return view('livewire.client.facility-detail');
    }
}

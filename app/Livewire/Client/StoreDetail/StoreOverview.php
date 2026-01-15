<?php

namespace App\Livewire\Client\StoreDetail;

use App\Models\Asset;
use App\Models\ClientAccount;
use App\Models\Store;
use Livewire\Component;

class StoreOverview extends Component
{
    public Store $store;

    public ClientAccount $clientAccount;

    public function mount(Store $store, ClientAccount $clientAccount)
    {
        $this->store = $store;
        $this->clientAccount = $clientAccount;
    }

    public function getTotalAssetsProperty()
    {
        return Asset::where('store_id', $this->store->id)->count();
    }

    public function getLowStockAssetsProperty()
    {
        return Asset::where('store_id', $this->store->id)
            ->whereColumn('units', '<=', 'minimum')
            ->get();
    }

    public function getAssetsByTypeProperty()
    {
        return Asset::where('store_id', $this->store->id)
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');
    }

    public function getRecentActivityProperty()
    {
        return Asset::where('store_id', $this->store->id)
            ->with(['user'])
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.client.store-detail.store-overview');
    }
}

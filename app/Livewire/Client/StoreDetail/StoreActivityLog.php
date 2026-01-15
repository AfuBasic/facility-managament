<?php

namespace App\Livewire\Client\StoreDetail;

use App\Models\AssetHistory;
use App\Models\ClientAccount;
use App\Models\Store;
use Livewire\Component;
use Livewire\WithPagination;

class StoreActivityLog extends Component
{
    use WithPagination;

    public Store $store;

    public ClientAccount $clientAccount;

    public $search = '';

    public $actionFilter = '';

    public function mount(Store $store, ClientAccount $clientAccount)
    {
        $this->store = $store;
        $this->clientAccount = $clientAccount;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function getLogsProperty()
    {
        return AssetHistory::whereHas('asset', function ($query) {
            $query->where('store_id', $this->store->id);
        })
            ->with(['asset', 'performedBy', 'targetUser'])
            ->when($this->search, function ($query) {
                $query->whereHas('asset', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('serial', 'like', '%'.$this->search.'%');
                })->orWhereHas('performedBy', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->actionFilter, function ($query) {
                $query->where('action_type', $this->actionFilter);
            })
            ->latest()
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.client.store-detail.store-activity-log', [
            'logs' => $this->logs,
        ]);
    }
}

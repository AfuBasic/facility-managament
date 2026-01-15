<?php

namespace App\Livewire\Client;

use App\Models\Asset;
use App\Models\ClientAccount;
use App\Models\Facility;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
class WorkOrderCreate extends Component
{
    public $title = '';

    public $description = '';

    public $priority = 'medium';

    public $facility_id = '';

    public $space_id = '';

    public $asset_id = '';

    public function mount()
    {
        //
    }

    public function getFacilitiesProperty()
    {
        $clientAccount = app(ClientAccount::class);

        return Facility::where('client_account_id', $clientAccount->id)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function getSpacesProperty()
    {
        if (! $this->facility_id) {
            return collect();
        }

        return \App\Models\Space::where('facility_id', $this->facility_id)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function getAssetsProperty()
    {
        if (! $this->facility_id) {
            return collect();
        }

        return Asset::where('facility_id', $this->facility_id)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function submit()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'facility_id' => 'required|exists:facilities,id',
            'space_id' => 'nullable|exists:spaces,id',
            'asset_id' => 'nullable|exists:assets,id',
        ]);

        WorkOrder::create([
            'facility_id' => $this->facility_id,
            'space_id' => $this->space_id ?: null,
            'asset_id' => $this->asset_id ?: null,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => 'reported',
            'reported_by' => Auth::id(),
            'reported_at' => now(),
        ]);

        session()->flash('success', 'Work order created successfully.');

        return redirect()->route('app.work-orders.index');
    }

    public function render()
    {
        return view('livewire.client.work-order-create');
    }
}

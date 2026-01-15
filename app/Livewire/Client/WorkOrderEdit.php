<?php

namespace App\Livewire\Client;

use App\Models\ClientAccount;
use App\Models\Facility;
use App\Models\Space;
use App\Models\WorkOrder;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
class WorkOrderEdit extends Component
{
    public WorkOrder $workOrder;

    public $title = '';

    public $description = '';

    public $priority = '';

    public $facility_id = '';

    public $space_id = '';

    public function mount(WorkOrder $workOrder)
    {
        // Only allow editing if work order is in 'reported' status
        if ($workOrder->status !== 'reported') {
            session()->flash('error', 'Work order can only be edited when in reported status.');

            return redirect()->route('app.work-orders.show', $workOrder);
        }

        $this->workOrder = $workOrder;
        $this->title = $workOrder->title;
        $this->description = $workOrder->description;
        $this->priority = $workOrder->priority;
        $this->facility_id = $workOrder->facility_id;
        $this->space_id = $workOrder->space_id;
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

        return Space::where('facility_id', $this->facility_id)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function update()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'facility_id' => 'required|exists:facilities,id',
            'space_id' => 'nullable|exists:spaces,id',
        ]);

        $this->workOrder->update([
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'facility_id' => $this->facility_id,
            'space_id' => $this->space_id ?: null,
        ]);

        session()->flash('success', 'Work order updated successfully.');

        return redirect()->route('app.work-orders.show', $this->workOrder);
    }

    public function render()
    {
        return view('livewire.client.work-order-edit');
    }
}

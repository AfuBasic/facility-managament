<?php

namespace App\Livewire\Client;

use App\Models\ClientAccount;
use App\Models\Facility;
use App\Models\Space;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.client-app')]
class WorkOrderList extends Component
{
    use WithPagination;

    #[Url]
    public $status = '';

    #[Url]
    public $priority = '';

    #[Url]
    public $search = '';

    public function mount()
    {
        $this->authorize('viewAny', WorkOrder::class);

        // If modal is opened via URL, initialize form for creating
        if ($this->showCreateModal) {
            $this->resetCreateForm();
        }
    }

    public function render()
    {
        $clientAccount = app(ClientAccount::class);
        $clientId = $clientAccount->id ?? session('current_client_account_id');
        $user = Auth::user();

        // Build base query
        $query = WorkOrder::where('client_account_id', $clientId)
            ->with(['facility', 'reportedBy', 'assignedTo']);

        // If user doesn't have general permission, only show their work orders
        if (! $user->can('view workorders')) {
            $query->where(function ($q) use ($user) {
                $q->where('reported_by', $user->id)
                    ->orWhere('assigned_to', $user->id);
            });
        }

        // Apply filters
        $workOrders = $query
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->priority, fn ($q) => $q->where('priority', $this->priority))
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('title', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.client.work-order-list', [
            'workOrders' => $workOrders,
        ]);
    }

    #[Url(as: 'create', history: true)]
    public $showCreateModal = false;

    public $newTitle = '';

    public $newDescription = '';

    public $newPriority = 'medium';

    public $newFacilityId = '';

    public $newSpaceId = '';

    public function getFacilitiesProperty()
    {
        $clientAccount = app(ClientAccount::class);
        $clientId = $clientAccount->id ?? session('current_client_account_id');

        return Facility::where('client_account_id', $clientId)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function getSpacesProperty()
    {
        if (! $this->newFacilityId) {
            return collect();
        }

        return Space::where('facility_id', $this->newFacilityId)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function openCreateModal()
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function resetCreateForm()
    {
        $this->newTitle = '';
        $this->newDescription = '';
        $this->newPriority = 'medium';
        $this->newFacilityId = '';
        $this->newSpaceId = '';
        $this->resetValidation();
        $this->showCreateModal = false;
    }

    public function saveWorkOrder()
    {
        $this->validate([
            'newTitle' => 'required|string|max:255',
            'newDescription' => 'required|string',
            'newPriority' => 'required|in:low,medium,high,critical',
            'newFacilityId' => 'required|exists:facilities,id',
            'newSpaceId' => 'nullable|exists:spaces,id',
        ]);

        WorkOrder::create([
            'facility_id' => $this->newFacilityId,
            'space_id' => $this->newSpaceId ?: null,
            'title' => $this->newTitle,
            'description' => $this->newDescription,
            'priority' => $this->newPriority,
            'status' => 'reported',
            'reported_by' => Auth::id(),
            'reported_at' => now(),
        ]);

        $this->showCreateModal = false;
        $this->resetCreateForm();
        session()->flash('success', 'Work order created successfully.');
    }
}

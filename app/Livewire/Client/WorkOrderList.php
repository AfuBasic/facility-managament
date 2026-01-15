<?php

namespace App\Livewire\Client;

use App\Models\ClientAccount;
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
        if (!$user->can('view workorders')) {
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
}

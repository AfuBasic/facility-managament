<?php

namespace App\Livewire\Client;

use App\Models\Asset;
use App\Models\ClientAccount;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderAsset;
use App\Models\Facility;
use App\Models\Space;
use App\Services\SlaCalculatorService;
use App\Services\WorkOrderStateManager;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
class WorkOrderDetail extends Component
{
    public WorkOrder $workOrder;

    public ClientAccount $clientAccount;

    #[Url(as: 'tab')]
    public $activeTab = 'details'; // details, history, updates

    // Modal states
    public $showApproveModal = false;

    public $showRejectModal = false;

    public $showAssignModal = false;

    public $showStartModal = false;

    public $showUpdateModal = false;

    public $showCompleteModal = false;

    public $showCloseModal = false;

    public $showReopenModal = false;

    public $showReassignModal = false;

    public $showDeleteModal = false;

    // Form fields
    public $approval_note = '';

    public $rejection_reason = '';

    public $assigned_user_id = '';

    public $assignment_note = '';

    public $selected_assets = [];

    public $update_note = '';

    public $update_time_spent = '';

    public $completion_notes = '';

    public $time_spent = '';

    public $total_cost = '';

    public $closure_note = '';

    public $reopen_reason = '';

    public $reassign_user_id = '';

    public $reassign_reason = '';

    public function hydrate()
    {
        if ($this->clientAccount) {
            setPermissionsTeamId($this->clientAccount->id);
        }
    }

    public function mount(WorkOrder $workOrder)
    {
        $this->authorize('view', $workOrder);

        $this->workOrder = $workOrder->load([
            'facility',
            'asset',
            'reportedBy',
            'approvedBy',
            'assignedTo',
            'assignedBy',
            'completedBy',
            'closedBy',
            'history.changedBy',
        ]);

        $this->clientAccount = app(ClientAccount::class);
    }

    public function getUsersProperty()
    {
        return User::orderBy('name')->pluck('name', 'id');
    }

    public function getReassignableUsersProperty()
    {
        return User::where('id', '!=', $this->workOrder->assigned_to)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function getUpdatesProperty()
    {
        return $this->workOrder->history
            ->filter(fn ($log) => $log->previous_state === $log->new_state)
            ->sortByDesc('changed_at');
    }

    public function getStateChangesProperty()
    {
        return $this->workOrder->history()->latest()->get()
            ->filter(fn ($log) => $log->previous_state !== $log->new_state);
    }

    public function getSlaStatusProperty(): array
    {
        return app(SlaCalculatorService::class)->getSlaStatus($this->workOrder);
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function getAvailableAssetsProperty()
    {
        return Asset::where('facility_id', $this->workOrder->facility_id)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function approve(WorkOrderStateManager $stateManager)
    {
        $this->authorize('approve', $this->workOrder);

        $stateManager->approve($this->workOrder, Auth::user(), $this->approval_note);

        $this->workOrder->refresh();
        $this->showApproveModal = false;
        $this->approval_note = '';

        session()->flash('success', 'Work order approved successfully.');
    }

    public function reject(WorkOrderStateManager $stateManager)
    {
        $this->authorize('reject', $this->workOrder);

        $this->validate(['rejection_reason' => 'required|string']);

        $stateManager->reject($this->workOrder, Auth::user(), $this->rejection_reason);

        $this->workOrder->refresh();
        $this->showRejectModal = false;
        $this->rejection_reason = '';

        session()->flash('success', 'Work order rejected.');
    }

    public function assign(WorkOrderStateManager $stateManager)
    {
        $this->authorize('assign', $this->workOrder);

        $this->validate(['assigned_user_id' => 'required|exists:users,id']);

        // Save selected assets to work_order_assets table FIRST (before event is dispatched)
        if (! empty($this->selected_assets)) {
            foreach ($this->selected_assets as $assetId) {
                WorkOrderAsset::create([
                    'work_order_id' => $this->workOrder->id,
                    'asset_id' => $assetId,
                    'action' => 'reserved',
                    'user_id' => Auth::id(),
                ]);
            }
        }

        // Now assign (this dispatches the event, and assets will be in the DB)
        $assignee = User::findOrFail($this->assigned_user_id);
        $stateManager->assign($this->workOrder, $assignee, Auth::user(), $this->assignment_note);

        $this->workOrder->refresh();
        $this->showAssignModal = false;
        $this->reset(['assigned_user_id', 'assignment_note', 'selected_assets']);

        session()->flash('success', 'Work order assigned successfully.');
    }

    public function reassign(WorkOrderStateManager $stateManager)
    {
        $this->authorize('reassign', $this->workOrder);

        $this->validate([
            'reassign_user_id' => 'required|exists:users,id',
            'reassign_reason' => 'nullable|string|max:500',
        ]);

        $newAssignee = User::findOrFail($this->reassign_user_id);
        $stateManager->reassign($this->workOrder, $newAssignee, Auth::user(), $this->reassign_reason);

        $this->workOrder->refresh();
        $this->showReassignModal = false;
        $this->reset(['reassign_user_id', 'reassign_reason']);

        session()->flash('success', 'Work order reassigned successfully.');
    }

    public function getAssignmentHistoryProperty()
    {
        return $this->workOrder->assignments()
            ->with(['assignee', 'assigner', 'unassigner'])
            ->orderBy('assigned_at', 'desc')
            ->get();
    }

    public function start(WorkOrderStateManager $stateManager)
    {
        $this->authorize('start', $this->workOrder);

        $stateManager->start($this->workOrder, Auth::user());

        $this->workOrder->refresh();
        $this->showStartModal = false;

        session()->flash('success', 'Work started successfully.');
    }

    public function pause(WorkOrderStateManager $stateManager)
    {
        $stateManager->pause($this->workOrder, Auth::user());
        $this->workOrder->refresh();
        session()->flash('success', 'Work order paused.');
    }

    public function resume(WorkOrderStateManager $stateManager)
    {
        $stateManager->resume($this->workOrder, Auth::user());
        $this->workOrder->refresh();
        session()->flash('success', 'Work order resumed.');
    }

    public function addUpdate(WorkOrderStateManager $stateManager)
    {
        $this->authorize('addUpdate', $this->workOrder);

        $this->validate([
            'update_note' => 'required|string',
            'update_time_spent' => 'nullable|integer|min:0',
        ]);

        $stateManager->addUpdate(
            $this->workOrder,
            Auth::user(),
            $this->update_note,
            $this->update_time_spent ?: null
        );

        $this->workOrder->refresh();
        $this->showUpdateModal = false;
        $this->reset(['update_note', 'update_time_spent']);

        session()->flash('success', 'Update added successfully.');
    }

    public function markDone(WorkOrderStateManager $stateManager)
    {
        $this->authorize('markDone', $this->workOrder);

        $this->validate([
            'completion_notes' => 'nullable|string',
            'time_spent' => 'nullable|integer|min:0',
            'total_cost' => 'nullable|numeric|min:0',
        ]);

        $stateManager->markDone($this->workOrder, Auth::user(), [
            'completion_notes' => $this->completion_notes,
            'time_spent' => $this->time_spent ?: null,
            'total_cost' => $this->total_cost ?: null,
        ]);

        $this->workOrder->refresh();
        $this->showCompleteModal = false;
        $this->reset(['completion_notes', 'time_spent', 'total_cost']);

        session()->flash('success', 'Work order marked as done. Awaiting creator approval.');
    }

    public function approveCompletion(WorkOrderStateManager $stateManager)
    {
        $this->authorize('approveCompletion', $this->workOrder);

        $stateManager->approveCompletion($this->workOrder, Auth::user());
        $this->workOrder->refresh();

        session()->flash('success', 'Work order approved and closed successfully.');
    }

    public function rejectCompletion(WorkOrderStateManager $stateManager)
    {
        $this->authorize('rejectCompletion', $this->workOrder);

        $this->validate([
            'rejection_reason' => 'required|string|min:5',
        ]);

        $stateManager->rejectCompletion($this->workOrder, Auth::user(), $this->rejection_reason);
        $this->workOrder->refresh();
        $this->showRejectModal = false;
        $this->rejection_reason = '';

        session()->flash('success', 'Work order sent back for more work.');
    }

    public function close(WorkOrderStateManager $stateManager)
    {
        $this->authorize('close', $this->workOrder);

        $stateManager->close($this->workOrder, Auth::user(), $this->closure_note);

        $this->workOrder->refresh();
        $this->showCloseModal = false;
        $this->closure_note = '';

        session()->flash('success', 'Work order closed.');
    }

    public function delete()
    {
        $this->authorize('delete', $this->workOrder);

        $workOrderId = $this->workOrder->id;
        $this->workOrder->delete();

        session()->flash('success', "Work order #{$workOrderId} deleted successfully.");

        return redirect()->route('app.work-orders.index');
    }

    public function reopen(WorkOrderStateManager $stateManager)
    {
        $this->authorize('reopen', $this->workOrder);

        $this->validate([
            'reopen_reason' => 'nullable|string|max:500',
        ]);

        $stateManager->reopen($this->workOrder, Auth::user(), $this->reopen_reason);

        $this->workOrder->refresh();
        $this->showReopenModal = false;
        $this->reopen_reason = '';

        session()->flash('success', 'Work order reopened.');
    }

    public $showEditModal = false;
    public $editTitle = '';
    public $editDescription = '';
    public $editPriority = '';
    public $editFacilityId = '';
    public $editSpaceId = '';

    public function openEditModal()
    {
        if ($this->workOrder->status !== 'reported') {
            session()->flash('error', 'Cannot edit work order unless status is reported.');
            return;
        }
        $this->editTitle = $this->workOrder->title;
        $this->editDescription = $this->workOrder->description;
        $this->editPriority = $this->workOrder->priority;
        $this->editFacilityId = $this->workOrder->facility_id;
        $this->editSpaceId = $this->workOrder->space_id;
        $this->showEditModal = true;
    }

    public function updateWorkOrder()
    {
         // Assuming policy has 'update'
         $this->authorize('update', $this->workOrder);
         
         $this->validate([
            'editTitle' => 'required|string|max:255',
            'editDescription' => 'required|string',
            'editPriority' => 'required|in:low,medium,high,critical',
            'editFacilityId' => 'required|exists:facilities,id',
            'editSpaceId' => 'nullable|exists:spaces,id',
         ]);

         $this->workOrder->update([
            'title' => $this->editTitle,
            'description' => $this->editDescription,
            'priority' => $this->editPriority,
            'facility_id' => $this->editFacilityId,
            'space_id' => $this->editSpaceId ?: null,
         ]);

         $this->showEditModal = false;
         $this->workOrder->refresh();
         session()->flash('success', 'Work order updated successfully.');
    }

    public function getFacilitiesProperty()
    {
        $clientId = $this->clientAccount->id ?? session('current_client_account_id');

        return Facility::where('client_account_id', $clientId)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function getSpacesProperty()
    {
        if (! $this->editFacilityId) {
            return collect();
        }

        return Space::where('facility_id', $this->editFacilityId)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function render()
    {
        return view('livewire.client.work-order-detail');
    }
}

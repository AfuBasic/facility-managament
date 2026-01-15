<?php

namespace App\Livewire\Client;

use App\Models\ClientAccount;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\WorkOrderStateManager;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
class WorkOrderDetail extends Component
{
    public WorkOrder $workOrder;

    public ClientAccount $clientAccount;

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

    // Form fields
    public $approval_note = '';

    public $rejection_reason = '';

    public $assigned_user_id = '';

    public $assignment_note = '';

    public $update_note = '';

    public $update_time_spent = '';

    public $completion_notes = '';

    public $time_spent = '';

    public $total_cost = '';

    public $closure_note = '';

    public $reopen_reason = '';

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

    public function getUpdatesProperty()
    {
        return $this->workOrder->history
            ->filter(fn($log) => $log->previous_state === $log->new_state)
            ->sortByDesc('changed_at');
    }

    public function getStateChangesProperty()
    {
        return $this->workOrder->history
            ->filter(fn($log) => $log->previous_state !== $log->new_state);
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
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

        $assignee = User::findOrFail($this->assigned_user_id);
        $stateManager->assign($this->workOrder, $assignee, Auth::user(), $this->assignment_note);

        $this->workOrder->refresh();
        $this->showAssignModal = false;
        $this->reset(['assigned_user_id', 'assignment_note']);

        session()->flash('success', 'Work order assigned successfully.');
    }

    public function start(WorkOrderStateManager $stateManager)
    {
        $this->authorize('start', $this->workOrder);

        $stateManager->start($this->workOrder, Auth::user());

        $this->workOrder->refresh();
        $this->showStartModal = false;

        session()->flash('success', 'Work started successfully.');
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

    public function complete(WorkOrderStateManager $stateManager)
    {
        $this->authorize('complete', $this->workOrder);

        $this->validate([
            'completion_notes' => 'nullable|string',
            'time_spent' => 'nullable|integer|min:0',
            'total_cost' => 'nullable|numeric|min:0',
        ]);

        $stateManager->complete($this->workOrder, Auth::user(), [
            'completion_notes' => $this->completion_notes,
            'time_spent' => $this->time_spent ?: null,
            'total_cost' => $this->total_cost ?: null,
        ]);

        $this->workOrder->refresh();
        $this->showCompleteModal = false;
        $this->reset(['completion_notes', 'time_spent', 'total_cost']);

        session()->flash('success', 'Work order marked as completed.');
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

    public function render()
    {
        return view('livewire.client.work-order-detail');
    }
}

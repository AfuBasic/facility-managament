<?php

namespace App\Services;

use App\Events\WorkOrderApproved;
use App\Events\WorkOrderAssigned;
use App\Events\WorkOrderClosed;
use App\Events\WorkOrderCompleted;
use App\Events\WorkOrderCompletionRejected;
use App\Events\WorkOrderRejected;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderAssignment;
use App\Models\WorkOrderHistory;
use Illuminate\Support\Facades\DB;

class WorkOrderStateManager
{
    /**
     * Approve a reported work order
     */
    public function approve(WorkOrder $workOrder, User $user, ?string $note = null): void
    {
        if (! $workOrder->canApprove()) {
            throw new \Exception("Work order cannot be approved in current state: {$workOrder->status}");
        }

        DB::transaction(function () use ($workOrder, $user, $note) {
            $previousState = $workOrder->status;

            $workOrder->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'approval_note' => $note,
            ]);

            $this->recordHistory($workOrder, $previousState, 'approved', $user, $note);
        });

        // Dispatch event
        WorkOrderApproved::dispatch($workOrder);
    }

    /**
     * Reject a reported work order
     */
    public function reject(WorkOrder $workOrder, User $user, string $reason): void
    {
        if (! $workOrder->canReject()) {
            throw new \Exception("Work order cannot be rejected in current state: {$workOrder->status}");
        }

        DB::transaction(function () use ($workOrder, $user, $reason) {
            $previousState = $workOrder->status;

            $workOrder->update([
                'status' => 'rejected',
                'rejected_by' => $user->id,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $this->recordHistory($workOrder, $previousState, 'rejected', $user, "Rejected: {$reason}");
        });

        // Dispatch event
        WorkOrderRejected::dispatch($workOrder);
    }

    /**
     * Assign an approved work order to a user
     */
    public function assign(WorkOrder $workOrder, User $assignee, User $assigner, ?string $note = null): void
    {
        if (! $workOrder->canAssign()) {
            throw new \Exception("Work order cannot be assigned in current state: {$workOrder->status}");
        }

        DB::transaction(function () use ($workOrder, $assignee, $assigner, $note) {
            $previousState = $workOrder->status;

            $workOrder->update([
                'status' => 'assigned',
                'assigned_to' => $assignee->id,
                'assigned_by' => $assigner->id,
                'assigned_at' => now(),
                'assignment_note' => $note,
            ]);

            // Create assignment record
            WorkOrderAssignment::create([
                'work_order_id' => $workOrder->id,
                'assigned_to' => $assignee->id,
                'assigned_by' => $assigner->id,
                'assigned_at' => now(),
                'assignment_note' => $note,
                'is_current' => true,
            ]);

            $this->recordHistory($workOrder, $previousState, 'assigned', $assigner, "Assigned to {$assignee->name}. {$note}");
        });

        // Dispatch event
        WorkOrderAssigned::dispatch($workOrder);
    }

    /**
     * Reassign a work order to a different user
     */
    public function reassign(WorkOrder $workOrder, User $newAssignee, User $reassigner, ?string $reason = null): void
    {
        if (! $workOrder->canReassign()) {
            throw new \Exception("Work order cannot be reassigned in current state: {$workOrder->status}");
        }

        $previousAssignee = $workOrder->assignedTo;

        DB::transaction(function () use ($workOrder, $newAssignee, $reassigner, $reason, $previousAssignee) {
            // Mark current assignment as no longer current
            WorkOrderAssignment::where('work_order_id', $workOrder->id)
                ->where('is_current', true)
                ->update([
                    'is_current' => false,
                    'unassigned_by' => $reassigner->id,
                    'unassigned_at' => now(),
                    'unassignment_reason' => $reason,
                ]);

            // Update work order with new assignee
            $workOrder->update([
                'assigned_to' => $newAssignee->id,
                'assigned_by' => $reassigner->id,
                'assigned_at' => now(),
                'assignment_note' => $reason,
            ]);

            // Create new assignment record
            WorkOrderAssignment::create([
                'work_order_id' => $workOrder->id,
                'assigned_to' => $newAssignee->id,
                'assigned_by' => $reassigner->id,
                'assigned_at' => now(),
                'assignment_note' => $reason,
                'is_current' => true,
            ]);

            $previousName = $previousAssignee ? $previousAssignee->name : 'unassigned';
            $this->recordHistory(
                $workOrder,
                $workOrder->status,
                $workOrder->status,
                $reassigner,
                "Reassigned from {$previousName} to {$newAssignee->name}. {$reason}"
            );
        });

        // Dispatch event to notify the new assignee
        WorkOrderAssigned::dispatch($workOrder);
    }

    /**
     * Start work on an assigned work order
     */
    public function start(WorkOrder $workOrder, User $user): void
    {
        if (! $workOrder->canStart()) {
            throw new \Exception("Work order cannot be started in current state: {$workOrder->status}");
        }

        DB::transaction(function () use ($workOrder, $user) {
            $previousState = $workOrder->status;

            $workOrder->update([
                'status' => 'in_progress',
                'started_by' => $user->id,
                'started_at' => now(),
            ]);

            $this->recordHistory($workOrder, $previousState, 'in_progress', $user, 'Work started');
        });

        // Dispatch event
        \App\Events\WorkOrderStarted::dispatch($workOrder);
    }

    /**
     * Pause a work order (put on hold)
     */
    public function pause(WorkOrder $workOrder, User $user, ?string $reason = null): void
    {
        if (! $workOrder->canPause()) {
            throw new \Exception("Work order cannot be paused in current state: {$workOrder->status}");
        }

        DB::transaction(function () use ($workOrder, $user, $reason) {
            $previousState = $workOrder->status;

            $workOrder->update([
                'status' => 'on_hold',
            ]);

            $this->recordHistory($workOrder, $previousState, 'on_hold', $user, $reason ?? 'Work paused');
        });
    }

    /**
     * Resume a paused work order
     */
    public function resume(WorkOrder $workOrder, User $user, ?string $note = null): void
    {
        if (! $workOrder->canResume()) {
            throw new \Exception("Work order cannot be resumed in current state: {$workOrder->status}");
        }

        DB::transaction(function () use ($workOrder, $user, $note) {
            $previousState = $workOrder->status;

            $workOrder->update([
                'status' => 'in_progress',
            ]);

            $this->recordHistory($workOrder, $previousState, 'in_progress', $user, $note ?? 'Work resumed');
        });
    }

    /**
     * Add an update/note to a work order in progress
     */
    public function addUpdate(WorkOrder $workOrder, User $user, string $note, ?int $timeSpent = null): void
    {
        if (! $workOrder->canReceiveUpdates()) {
            throw new \Exception("Work order cannot receive updates in current state: {$workOrder->status}");
        }

        DB::transaction(function () use ($workOrder, $user, $note, $timeSpent) {
            // Record update in history (status doesn't change)
            $this->recordHistory($workOrder, $workOrder->status, $workOrder->status, $user, $note);

            // Optionally update time spent
            if ($timeSpent !== null) {
                $workOrder->increment('time_spent', $timeSpent);
            }
        });

        // Dispatch event for update notification
        \App\Events\WorkOrderUpdateAdded::dispatch($workOrder, $user);
    }

    /**
     * Mark work as done (pending creator approval)
     */
    public function markDone(WorkOrder $workOrder, User $user, array $data): void
    {
        if (! $workOrder->canMarkDone()) {
            throw new \Exception("Work order cannot be marked as done in current state: {$workOrder->status}");
        }

        DB::transaction(function () use ($workOrder, $user, $data) {
            $previousState = $workOrder->status;

            $workOrder->update([
                'status' => 'completed',
                'completed_by' => $user->id,
                'completed_at' => now(),
                'completion_notes' => $data['completion_notes'] ?? null,
                'time_spent' => $data['time_spent'] ?? null,
                'total_cost' => $data['total_cost'] ?? null,
            ]);

            $this->recordHistory($workOrder, $previousState, 'completed', $user, $data['completion_notes'] ?? 'Work marked as completed');
        });

        // Dispatch event
        WorkOrderCompleted::dispatch($workOrder);
    }

    /**
     * Approve completion (creator approves the done work and closes the work order)
     */
    public function approveCompletion(WorkOrder $workOrder, User $user, ?string $note = null): void
    {
        if (! $workOrder->canApproveCompletion()) {
            throw new \Exception("Work order completion cannot be approved in current state: {$workOrder->status}");
        }

        DB::transaction(function () use ($workOrder, $user, $note) {
            $previousState = $workOrder->status;

            $workOrder->update([
                'status' => 'closed',
                'closed_by' => $user->id,
                'closed_at' => now(),
                'closure_note' => $note,
            ]);

            $this->recordHistory($workOrder, $previousState, 'closed', $user, $note ?? 'Work approved and closed by creator');
        });

        // Dispatch event to notify both creator and assignee
        WorkOrderClosed::dispatch($workOrder);
    }

    /**
     * Reject completion (creator rejects, work goes back to in_progress)
     */
    public function rejectCompletion(WorkOrder $workOrder, User $user, string $reason): void
    {
        if (! $workOrder->canRejectCompletion()) {
            throw new \Exception("Work order completion cannot be rejected in current state: {$workOrder->status}");
        }

        DB::transaction(function () use ($workOrder, $user, $reason) {
            $previousState = $workOrder->status;

            $workOrder->update([
                'status' => 'in_progress',
            ]);

            $this->recordHistory($workOrder, $previousState, 'in_progress', $user, "Completion rejected: {$reason}");
        });

        // Dispatch event
        WorkOrderCompletionRejected::dispatch($workOrder, $user, $reason);
    }

    /**
     * Close a completed work order
     */
    public function close(WorkOrder $workOrder, User $user, ?string $note = null): void
    {
        if (! $workOrder->canClose()) {
            throw new \Exception("Work order cannot be closed in current state: {$workOrder->status}");
        }

        DB::transaction(function () use ($workOrder, $user, $note) {
            $previousState = $workOrder->status;

            $workOrder->update([
                'status' => 'closed',
                'closed_by' => $user->id,
                'closed_at' => now(),
                'closure_note' => $note,
            ]);

            $this->recordHistory($workOrder, $previousState, 'closed', $user, $note ?? 'Work order closed');
        });

        // Dispatch event
        WorkOrderClosed::dispatch($workOrder);
    }

    /**
     * Reopen a completed work order
     */
    public function reopen(WorkOrder $workOrder, User $user, ?string $reason = null): void
    {
        if (! $workOrder->canReopen()) {
            throw new \Exception("Work order cannot be reopened in current state: {$workOrder->status}");
        }

        DB::transaction(function () use ($workOrder, $user, $reason) {
            $previousState = $workOrder->status;

            $workOrder->update([
                'status' => 'in_progress',
            ]);

            $this->recordHistory($workOrder, $previousState, 'in_progress', $user, $reason ? "Reopened: {$reason}" : 'Work order reopened');
        });
    }

    /**
     * Record state transition in history
     */
    protected function recordHistory(
        WorkOrder $workOrder,
        ?string $previousState,
        string $newState,
        User $user,
        ?string $note = null
    ): void {
        WorkOrderHistory::create([
            'work_order_id' => $workOrder->id,
            'previous_state' => $previousState,
            'new_state' => $newState,
            'changed_by' => $user->id,
            'changed_at' => now(),
            'note' => $note,
        ]);
    }
}

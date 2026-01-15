<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;

class WorkOrderPolicy
{
    /**
     * Determine if the user can view any work orders.
     */
    public function viewAny(User $user): bool
    {
        // User can view work orders if they:
        // 1. Have the permission
        // 2. Have created any work orders
        // 3. Have been assigned any work orders
        return $user->can('view workorders')
            || WorkOrder::where('reported_by', $user->id)->exists()
            || WorkOrder::where('assigned_to', $user->id)->exists();
    }

    /**
     * Determine if the user can view the work order.
     */
    public function view(User $user, WorkOrder $workOrder): bool
    {
        // User can view if they:
        // 1. Have the permission
        // 2. Are the creator
        // 3. Are the assignee
        return $user->can('view workorders')
            || $workOrder->isCreator($user)
            || $workOrder->isAssignee($user);
    }

    /**
     * Determine if the user can create work orders.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create work orders
        return true;
    }

    /**
     * Determine if the user can update the work order.
     */
    public function update(User $user, WorkOrder $workOrder): bool
    {
        // Only creator can edit, and only when status is 'reported'
        return $workOrder->isCreator($user) && $workOrder->status === 'reported';
    }

    /**
     * Determine if the user can approve the work order.
     */
    public function approve(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('approve workorders') && $workOrder->canApprove();
    }

    /**
     * Determine if the user can reject the work order.
     */
    public function reject(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('approve workorders') && $workOrder->canReject();
    }

    /**
     * Determine if the user can assign the work order.
     */
    public function assign(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('assign workorders') && $workOrder->canAssign();
    }

    /**
     * Determine if the user can reassign the work order.
     */
    public function reassign(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('assign workorders') && $workOrder->canReassign();
    }

    /**
     * Determine if the user can start work on the work order.
     */
    public function start(User $user, WorkOrder $workOrder): bool
    {
        // Only the assignee can start work
        return $workOrder->isAssignee($user) && $workOrder->canStart();
    }

    /**
     * Determine if the user can add updates to the work order.
     */
    public function addUpdate(User $user, WorkOrder $workOrder): bool
    {
        // Creator or assignee can add updates when status is in_progress
        return ($workOrder->isCreator($user) || $workOrder->isAssignee($user))
            && $workOrder->canReceiveUpdates();
    }

    /**
     * Determine if the user can pause the work order.
     */
    public function pause(User $user, WorkOrder $workOrder): bool
    {
        // Admin or assignee can pause
        return ($user->hasRole('admin') || $workOrder->isAssignee($user))
            && $workOrder->canPause();
    }

    /**
     * Determine if the user can mark the work order as done.
     */
    public function markDone(User $user, WorkOrder $workOrder): bool
    {
        // Admin, creator, or assignee can mark as done
        return ($user->hasRole('admin') || $workOrder->isCreator($user) || $workOrder->isAssignee($user))
            && $workOrder->canMarkDone();
    }

    /**
     * Determine if the user can approve the completion.
     */
    public function approveCompletion(User $user, WorkOrder $workOrder): bool
    {
        // Only creator can approve completion
        return $workOrder->isCreator($user) && $workOrder->canApproveCompletion();
    }

    /**
     * Determine if the user can reject the completion.
     */
    public function rejectCompletion(User $user, WorkOrder $workOrder): bool
    {
        // Only creator can reject completion
        return $workOrder->isCreator($user) && $workOrder->canRejectCompletion();
    }

    /**
     * Determine if the user can close the work order.
     */
    public function close(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('close workorders') && $workOrder->canClose();
    }

    /**
     * Determine if the user can reopen the work order.
     */
    public function reopen(User $user, WorkOrder $workOrder): bool
    {
        // Only creator can reopen
        return $workOrder->isCreator($user) && $workOrder->canReopen();
    }

    /**
     * Determine if the user can delete the work order.
     */
    public function delete(User $user, WorkOrder $workOrder): bool
    {
        // Only creator can delete, and only when status is 'reported'
        return $workOrder->isCreator($user) && $workOrder->status === 'reported';
    }
}

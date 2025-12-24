<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;

class WorkOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       return $user->can('workorder.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkOrder $workOrder): bool
    {
        $client = app('currentClient');
        return $workOrder->clientAccount()->is($client) && $user->can('work-orders.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('workorder.create');
    }

    /**
     * Determin whether the user can assign workorders
     */
    public function assign(User $user, WorkOrder $workOrder): bool
    {
        $client = app('currentClient');
        return $workOrder->clientAccount()->is($client) && $user->can('workorder.assign');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkOrder $workOrder): bool
    {
        $client = app('currentClient');
        return $workOrder->clientAccount()->is($client) && $user->can('workorder.update');
    }

    /**
     * Determine where the user can complete a workorder
     */

    public function complete(User $user, WorkOrder $workOrder): bool
    {
        $client = app('currentClient');
        if (! $workOrder->clientAccount()->is($client)) {
            return false;
        }

        // Explicit override capability
        if ($user->can('work-orders.override-complete')) {
            return true;
        }

        // Otherwise, must be the currently assigned user
        $activeAssignment = $workOrder->assignments()
            ->whereNull('ended_at')
            ->first();

        return $activeAssignment
            && $activeAssignment->assignedTo()->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkOrder $workOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkOrder $workOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkOrder $workOrder): bool
    {
        return false;
    }
}

<?php

namespace App\Listeners;

use App\Events\WorkOrderReopened;
use App\Notifications\WorkOrderReopenedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWorkOrderReopenedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderReopened $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'assignedTo', 'facility']);
        $reopenedBy = $event->reopenedBy;

        // Notify the other party
        if ($reopenedBy->id === $workOrder->reported_by) {
            // Creator reopened, notify the assignee
            if ($workOrder->assignedTo) {
                $workOrder->assignedTo->notify(new WorkOrderReopenedNotification($workOrder, $reopenedBy, $event->reason));
            }
        } else {
            // Assignee (or someone else) reopened, notify the creator
            if ($workOrder->reportedBy) {
                $workOrder->reportedBy->notify(new WorkOrderReopenedNotification($workOrder, $reopenedBy, $event->reason));
            }
        }
    }
}

<?php

namespace App\Listeners;

use App\Events\WorkOrderResumed;
use App\Notifications\WorkOrderResumedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWorkOrderResumedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderResumed $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'assignedTo', 'facility']);
        $resumedBy = $event->resumedBy;

        // Notify the other party
        if ($resumedBy->id === $workOrder->reported_by) {
            // Creator resumed, notify the assignee
            if ($workOrder->assignedTo) {
                $workOrder->assignedTo->notify(new WorkOrderResumedNotification($workOrder, $resumedBy));
            }
        } else {
            // Assignee (or someone else) resumed, notify the creator
            if ($workOrder->reportedBy) {
                $workOrder->reportedBy->notify(new WorkOrderResumedNotification($workOrder, $resumedBy));
            }
        }
    }
}

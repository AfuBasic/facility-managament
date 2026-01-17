<?php

namespace App\Listeners;

use App\Events\WorkOrderPaused;
use App\Notifications\WorkOrderPausedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWorkOrderPausedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderPaused $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'assignedTo', 'facility']);
        $pausedBy = $event->pausedBy;

        // Notify the other party
        if ($pausedBy->id === $workOrder->reported_by) {
            // Creator paused, notify the assignee
            if ($workOrder->assignedTo) {
                $workOrder->assignedTo->notify(new WorkOrderPausedNotification($workOrder, $pausedBy, $event->reason));
            }
        } else {
            // Assignee (or someone else) paused, notify the creator
            if ($workOrder->reportedBy) {
                $workOrder->reportedBy->notify(new WorkOrderPausedNotification($workOrder, $pausedBy, $event->reason));
            }
        }
    }
}

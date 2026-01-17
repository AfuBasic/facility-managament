<?php

namespace App\Listeners;

use App\Events\WorkOrderClosed;
use App\Mail\WorkOrderClosedMail;
use App\Notifications\WorkOrderClosedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderClosedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderClosed $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'assignedTo', 'closedBy', 'facility']);

        // Notify the other party - if closer is creator, notify assignee; if closer is assignee, notify creator
        if ($workOrder->closed_by === $workOrder->reported_by) {
            // Creator closed, notify the assignee
            if ($workOrder->assignedTo) {
                $workOrder->assignedTo->notify(new WorkOrderClosedNotification($workOrder));
                Mail::to($workOrder->assignedTo->email)
                    ->queue(new WorkOrderClosedMail($workOrder));
            }
        } else {
            // Assignee (or someone else) closed, notify the creator
            if ($workOrder->reportedBy) {
                $workOrder->reportedBy->notify(new WorkOrderClosedNotification($workOrder));
                Mail::to($workOrder->reportedBy->email)
                    ->queue(new WorkOrderClosedMail($workOrder));
            }
        }
    }
}

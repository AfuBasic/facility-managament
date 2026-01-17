<?php

namespace App\Listeners;

use App\Events\WorkOrderCompleted;
use App\Mail\WorkOrderCompletedMail;
use App\Notifications\WorkOrderCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderCompletedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderCompleted $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'assignedTo', 'completedBy', 'facility']);

        // Notify the other party - if creator marked done, notify assignee; if assignee marked done, notify creator
        if ($workOrder->completed_by === $workOrder->reported_by) {
            // Creator marked as done, notify the assignee
            if ($workOrder->assignedTo) {
                $workOrder->assignedTo->notify(new WorkOrderCompletedNotification($workOrder));
                Mail::to($workOrder->assignedTo->email)
                    ->queue(new WorkOrderCompletedMail($workOrder, $workOrder->assignedTo));
            }
        } else {
            // Assignee (or someone else) marked as done, notify the creator
            if ($workOrder->reportedBy) {
                $workOrder->reportedBy->notify(new WorkOrderCompletedNotification($workOrder));
                Mail::to($workOrder->reportedBy->email)
                    ->queue(new WorkOrderCompletedMail($workOrder, $workOrder->reportedBy));
            }
        }
    }
}

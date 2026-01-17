<?php

namespace App\Listeners;

use App\Events\WorkOrderStarted;
use App\Mail\WorkOrderStartedMail;
use App\Notifications\WorkorderStartedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderStartedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderStarted $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'assignedTo', 'startedBy', 'facility']);

        // Notify the other party - if creator started, notify assignee; if assignee started, notify creator
        if ($workOrder->started_by === $workOrder->reported_by) {
            // Creator started the work, notify the assignee
            if ($workOrder->assignedTo) {
                $workOrder->assignedTo->notify(new WorkorderStartedNotification($workOrder));
                Mail::to($workOrder->assignedTo->email)
                    ->queue(new WorkOrderStartedMail($workOrder));
            }
        } else {
            // Assignee (or someone else) started the work, notify the creator
            if ($workOrder->reportedBy) {
                $workOrder->reportedBy->notify(new WorkorderStartedNotification($workOrder));
                Mail::to($workOrder->reportedBy->email)
                    ->queue(new WorkOrderStartedMail($workOrder));
            }
        }
    }
}

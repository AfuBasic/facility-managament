<?php

namespace App\Listeners;

use App\Events\WorkOrderUpdateAdded;
use App\Mail\WorkOrderUpdateAddedMail;
use App\Notifications\WorkOrderUpdateAddedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderUpdateAddedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderUpdateAdded $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'assignedTo', 'facility']);
        $updatedBy = $event->updatedBy;

        // Send to the other party (if creator added update, notify assignee; if assignee added, notify creator)
        if ($workOrder->isCreator($updatedBy) && $workOrder->assignedTo) {
            // Creator added update, notify assignee
            $workOrder->assignedTo->notify(new WorkOrderUpdateAddedNotification($workOrder, $updatedBy));
            Mail::to($workOrder->assignedTo->email)
                ->queue(new WorkOrderUpdateAddedMail($workOrder, $updatedBy));
        } elseif ($workOrder->isAssignee($updatedBy) && $workOrder->reportedBy) {
            // Assignee added update, notify creator
            $workOrder->reportedBy->notify(new WorkOrderUpdateAddedNotification($workOrder, $updatedBy));
            Mail::to($workOrder->reportedBy->email)
                ->queue(new WorkOrderUpdateAddedMail($workOrder, $updatedBy));
        }
    }
}

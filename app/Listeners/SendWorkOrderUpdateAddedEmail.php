<?php

namespace App\Listeners;

use App\Events\WorkOrderUpdateAdded;
use App\Mail\WorkOrderUpdateAddedMail;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderUpdateAddedEmail
{
    public function handle(WorkOrderUpdateAdded $event): void
    {
        $workOrder = $event->workOrder;
        $updatedBy = $event->updatedBy;

        // Send to the other party (if creator added update, notify assignee; if assignee added, notify creator)
        if ($workOrder->isCreator($updatedBy) && $workOrder->assignedTo) {
            // Creator added update, notify assignee
            Mail::to($workOrder->assignedTo->email)
                ->send(new WorkOrderUpdateAddedMail($workOrder, $updatedBy));
        } elseif ($workOrder->isAssignee($updatedBy) && $workOrder->reportedBy) {
            // Assignee added update, notify creator
            Mail::to($workOrder->reportedBy->email)
                ->send(new WorkOrderUpdateAddedMail($workOrder, $updatedBy));
        }
    }
}

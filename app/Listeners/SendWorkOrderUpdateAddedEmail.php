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

        // Notify both parties (excluding the person who added the update)
        // If assignee exists and didn't add the update, notify them
        if ($workOrder->assignedTo && $workOrder->assignedTo->id !== $updatedBy->id) {
            $workOrder->assignedTo->notify(new WorkOrderUpdateAddedNotification($workOrder, $updatedBy));
            Mail::to($workOrder->assignedTo->email)
                ->queue(new WorkOrderUpdateAddedMail($workOrder, $updatedBy));
        }

        // If creator exists and didn't add the update, notify them
        if ($workOrder->reportedBy && $workOrder->reportedBy->id !== $updatedBy->id) {
            $workOrder->reportedBy->notify(new WorkOrderUpdateAddedNotification($workOrder, $updatedBy));
            Mail::to($workOrder->reportedBy->email)
                ->queue(new WorkOrderUpdateAddedMail($workOrder, $updatedBy));
        }
    }
}

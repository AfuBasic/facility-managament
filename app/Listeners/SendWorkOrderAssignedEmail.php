<?php

namespace App\Listeners;

use App\Events\WorkOrderAssigned;
use App\Mail\WorkOrderAssignedMail;
use App\Notifications\WorkOrderAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderAssignedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderAssigned $event): void
    {
        $workOrder = $event->workOrder->load(['assignedTo', 'assignedBy', 'facility', 'allocatedAssets.asset']);

        if (! $workOrder->assignedTo) {
            return;
        }

        // Send in-app notification
        $workOrder->assignedTo->notify(new WorkOrderAssignedNotification($workOrder));

        // Send email
        Mail::to($workOrder->assignedTo->email)
            ->send(new WorkOrderAssignedMail($workOrder));
    }
}

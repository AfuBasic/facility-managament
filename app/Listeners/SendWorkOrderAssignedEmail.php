<?php

namespace App\Listeners;

use App\Events\WorkOrderAssigned;
use App\Mail\WorkOrderAssignedMail;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderAssignedEmail
{
    public function handle(WorkOrderAssigned $event): void
    {
        $workOrder = $event->workOrder->load(['assignedTo', 'facility']);

        Mail::to($workOrder->assignedTo->email)
            ->send(new WorkOrderAssignedMail($workOrder));
    }
}

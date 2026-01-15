<?php

namespace App\Listeners;

use App\Events\WorkOrderClosed;
use App\Mail\WorkOrderClosedMail;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderClosedEmail
{
    public function handle(WorkOrderClosed $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'assignedTo', 'closedBy']);

        // Notify reporter
        Mail::to($workOrder->reportedBy->email)
            ->send(new WorkOrderClosedMail($workOrder));

        // Notify assignee if different from reporter
        if ($workOrder->assigned_to && $workOrder->assigned_to !== $workOrder->reported_by) {
            Mail::to($workOrder->assignedTo->email)
                ->send(new WorkOrderClosedMail($workOrder));
        }
    }
}

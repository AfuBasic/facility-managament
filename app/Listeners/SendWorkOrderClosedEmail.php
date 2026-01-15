<?php

namespace App\Listeners;

use App\Events\WorkOrderClosed;
use App\Mail\WorkOrderClosedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderClosedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderClosed $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'assignedTo', 'closedBy', 'facility']);

        // Notify reporter
        if ($workOrder->reportedBy) {
            Mail::to($workOrder->reportedBy->email)
                ->queue(new WorkOrderClosedMail($workOrder));
        }

        // Notify assignee if different from reporter
        if ($workOrder->assignedTo && $workOrder->assigned_to !== $workOrder->reported_by) {
            Mail::to($workOrder->assignedTo->email)
                ->queue(new WorkOrderClosedMail($workOrder));
        }
    }
}

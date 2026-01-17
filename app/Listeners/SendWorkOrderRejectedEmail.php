<?php

namespace App\Listeners;

use App\Events\WorkOrderRejected;
use App\Mail\WorkOrderRejectedMail;
use App\Notifications\WorkOrderRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderRejectedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderRejected $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'rejectedBy', 'assignedTo', 'facility']);

        // Notify the other party - if rejecter is creator, notify assignee; otherwise notify creator
        if ($workOrder->rejected_by === $workOrder->reported_by) {
            // Creator rejected, notify the assignee
            if ($workOrder->assignedTo) {
                $workOrder->assignedTo->notify(new WorkOrderRejectedNotification($workOrder));
                Mail::to($workOrder->assignedTo->email)
                    ->queue(new WorkOrderRejectedMail($workOrder));
            }
        } else {
            // Someone else rejected, notify the creator
            if ($workOrder->reportedBy) {
                $workOrder->reportedBy->notify(new WorkOrderRejectedNotification($workOrder));
                Mail::to($workOrder->reportedBy->email)
                    ->queue(new WorkOrderRejectedMail($workOrder));
            }
        }
    }
}

<?php

namespace App\Listeners;

use App\Events\WorkOrderApproved;
use App\Mail\WorkOrderApprovedMail;
use App\Notifications\WorkOrderApprovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderApprovedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderApproved $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'approvedBy', 'assignedTo', 'facility']);

        // Notify the other party - if approver is creator, notify assignee; otherwise notify creator
        if ($workOrder->approved_by === $workOrder->reported_by) {
            // Creator approved, notify the assignee
            if ($workOrder->assignedTo) {
                $workOrder->assignedTo->notify(new WorkOrderApprovedNotification($workOrder));
                Mail::to($workOrder->assignedTo->email)
                    ->queue(new WorkOrderApprovedMail($workOrder));
            }
        } else {
            // Someone else approved, notify the creator
            if ($workOrder->reportedBy) {
                $workOrder->reportedBy->notify(new WorkOrderApprovedNotification($workOrder));
                Mail::to($workOrder->reportedBy->email)
                    ->queue(new WorkOrderApprovedMail($workOrder));
            }
        }
    }
}

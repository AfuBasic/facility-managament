<?php

namespace App\Listeners;

use App\Events\WorkOrderCompletionRejected;
use App\Mail\WorkOrderCompletionRejectedMail;
use App\Notifications\WorkOrderCompletionRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderCompletionRejectedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderCompletionRejected $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'assignedTo', 'completedBy', 'facility']);

        // Notify the person who marked it complete (the completion was rejected)
        // Usually the assignee marks it complete and the creator rejects
        if ($workOrder->completed_by === $workOrder->assigned_to) {
            // Assignee marked complete, notify them of rejection
            if ($workOrder->assignedTo) {
                $workOrder->assignedTo->notify(new WorkOrderCompletionRejectedNotification($workOrder, $event->reason));
                Mail::to($workOrder->assignedTo->email)
                    ->queue(new WorkOrderCompletionRejectedMail(
                        $workOrder,
                        $event->rejectedBy,
                        $event->reason
                    ));
            }
        } else {
            // Someone else marked complete, notify them
            if ($workOrder->completedBy) {
                $workOrder->completedBy->notify(new WorkOrderCompletionRejectedNotification($workOrder, $event->reason));
                Mail::to($workOrder->completedBy->email)
                    ->queue(new WorkOrderCompletionRejectedMail(
                        $workOrder,
                        $event->rejectedBy,
                        $event->reason
                    ));
            }
        }
    }
}

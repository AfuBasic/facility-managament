<?php

namespace App\Listeners;

use App\Events\WorkOrderCompletionRejected;
use App\Mail\WorkOrderCompletionRejectedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderCompletionRejectedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderCompletionRejected $event): void
    {
        $workOrder = $event->workOrder->load(['assignedTo', 'facility']);

        // Send to the assignee to notify them the completion was rejected
        if ($workOrder->assignedTo) {
            Mail::to($workOrder->assignedTo->email)
                ->queue(new WorkOrderCompletionRejectedMail(
                    $workOrder,
                    $event->rejectedBy,
                    $event->reason
                ));
        }
    }
}

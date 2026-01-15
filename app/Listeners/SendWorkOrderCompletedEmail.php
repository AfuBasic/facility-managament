<?php

namespace App\Listeners;

use App\Events\WorkOrderCompleted;
use App\Mail\WorkOrderCompletedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderCompletedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderCompleted $event): void
    {
        $workOrder = $event->workOrder->load(['assignedTo', 'completedBy', 'facility']);

        // Send to the assignee to notify them the work order was completed
        if ($workOrder->assignedTo) {
            Mail::to($workOrder->assignedTo->email)
                ->queue(new WorkOrderCompletedMail($workOrder));
        }
    }
}

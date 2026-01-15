<?php

namespace App\Listeners;

use App\Events\WorkOrderStarted;
use App\Mail\WorkOrderStartedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderStartedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderStarted $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'assignedTo', 'facility']);

        // Send email to the creator (reporter) that work has started
        if ($workOrder->reportedBy) {
            Mail::to($workOrder->reportedBy->email)
                ->queue(new WorkOrderStartedMail($workOrder));
        }
    }
}

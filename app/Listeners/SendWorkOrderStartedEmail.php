<?php

namespace App\Listeners;

use App\Events\WorkOrderStarted;
use App\Mail\WorkOrderStartedMail;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderStartedEmail
{
    public function handle(WorkOrderStarted $event): void
    {
        // Send email to the creator (reporter) that work has started
        if ($event->workOrder->reportedBy) {
            Mail::to($event->workOrder->reportedBy->email)
                ->send(new WorkOrderStartedMail($event->workOrder));
        }
    }
}

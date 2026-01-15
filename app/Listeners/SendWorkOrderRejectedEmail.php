<?php

namespace App\Listeners;

use App\Events\WorkOrderRejected;
use App\Mail\WorkOrderRejectedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderRejectedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderRejected $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'rejectedBy', 'facility']);

        Mail::to($workOrder->reportedBy->email)
            ->queue(new WorkOrderRejectedMail($workOrder));
    }
}

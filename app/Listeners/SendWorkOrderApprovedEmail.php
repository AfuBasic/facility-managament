<?php

namespace App\Listeners;

use App\Events\WorkOrderApproved;
use App\Mail\WorkOrderApprovedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderApprovedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WorkOrderApproved $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'approvedBy', 'facility']);

        Mail::to($workOrder->reportedBy->email)
            ->send(new WorkOrderApprovedMail($workOrder));
    }
}

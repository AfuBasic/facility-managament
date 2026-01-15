<?php

namespace App\Listeners;

use App\Events\WorkOrderApproved;
use App\Mail\WorkOrderApprovedMail;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderApprovedEmail
{
    public function handle(WorkOrderApproved $event): void
    {
        $workOrder = $event->workOrder->load(['reportedBy', 'approvedBy']);

        Mail::to($workOrder->reportedBy->email)
            ->send(new WorkOrderApprovedMail($workOrder));
    }
}

<?php

namespace App\Listeners;

use App\Events\SlaResolutionBreached;
use App\Events\SlaResponseBreached;
use App\Mail\SlaBreachedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendSlaBreachedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle response SLA breaches.
     */
    public function handle(SlaResponseBreached|SlaResolutionBreached $event): void
    {
        $workOrder = $event->workOrder->load(['assignedTo', 'reportedBy', 'facility']);
        $breachType = $event instanceof SlaResponseBreached ? 'response' : 'resolution';

        // Notify the assignee if assigned
        if ($workOrder->assignedTo) {
            Mail::to($workOrder->assignedTo->email)
                ->queue(new SlaBreachedMail($workOrder, $breachType));
        }

        // Also notify facility manager or admin (if different from assignee)
        // Could add more recipients based on escalation rules
    }
}

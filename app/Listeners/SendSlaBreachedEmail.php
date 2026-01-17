<?php

namespace App\Listeners;

use App\Events\SlaResolutionBreached;
use App\Events\SlaResponseBreached;
use App\Mail\SlaBreachedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSlaBreachedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle SLA breach events.
     */
    public function handle(SlaResponseBreached|SlaResolutionBreached $event): void
    {
        $workOrder = $event->workOrder->load(['assignedTo', 'reportedBy', 'facility', 'clientAccount']);
        $breachType = $event instanceof SlaResponseBreached ? 'response' : 'resolution';

        $recipients = $this->getRecipients($workOrder);

        Log::info('SLA Breach Email Recipients', [
            'work_order_id' => $workOrder->id,
            'breach_type' => $breachType,
            'recipients' => $recipients,
            'assignee' => $workOrder->assignedTo?->email,
            'reporter' => $workOrder->reportedBy?->email,
            'notification_email' => $workOrder->clientAccount?->notification_email,
        ]);

        if (empty($recipients)) {
            Log::warning('No recipients found for SLA breach email', [
                'work_order_id' => $workOrder->id,
            ]);

            return;
        }

        foreach ($recipients as $email) {
            Mail::to($email)->queue(new SlaBreachedMail($workOrder, $breachType));
        }
    }

    /**
     * Get the recipients for the SLA breach notification.
     */
    protected function getRecipients($workOrder): array
    {
        $recipients = [];

        // Priority 1: If work order is assigned, notify the assignee
        if ($workOrder->assignedTo) {
            $recipients[] = $workOrder->assignedTo->email;
        }

        // Priority 2: If not assigned, use the client notification email
        if (! $workOrder->assignedTo && $workOrder->clientAccount?->notification_email) {
            $recipients[] = $workOrder->clientAccount->notification_email;
        }

        // Priority 3: If still no recipients, notify the reporter (who created it)
        if (empty($recipients) && $workOrder->reportedBy) {
            $recipients[] = $workOrder->reportedBy->email;
        }

        return array_unique(array_filter($recipients));
    }
}

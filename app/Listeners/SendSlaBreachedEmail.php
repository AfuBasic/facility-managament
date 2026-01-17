<?php

namespace App\Listeners;

use App\Events\SlaResolutionBreached;
use App\Events\SlaResponseBreached;
use App\Mail\SlaBreachedMail;
use App\Notifications\SlaBreachedNotification;
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

        // Send in-app notifications
        $this->sendInAppNotifications($workOrder, $breachType);

        // Send email notifications
        $this->sendEmailNotifications($workOrder, $breachType);
    }

    /**
     * Send in-app notifications to relevant users.
     */
    protected function sendInAppNotifications($workOrder, string $breachType): void
    {
        $notifiedUsers = [];

        // Notify assignee
        if ($workOrder->assignedTo) {
            $workOrder->assignedTo->notify(new SlaBreachedNotification($workOrder, $breachType));
            $notifiedUsers[] = $workOrder->assignedTo->id;
        }

        // Notify reporter if different from assignee
        if ($workOrder->reportedBy && ! in_array($workOrder->reportedBy->id, $notifiedUsers)) {
            $workOrder->reportedBy->notify(new SlaBreachedNotification($workOrder, $breachType));
        }
    }

    /**
     * Send email notifications.
     */
    protected function sendEmailNotifications($workOrder, string $breachType): void
    {
        $recipients = $this->getEmailRecipients($workOrder);

        Log::info('SLA Breach Email Recipients', [
            'work_order_id' => $workOrder->id,
            'breach_type' => $breachType,
            'recipients' => $recipients,
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
     * Get email recipients for the SLA breach notification.
     */
    protected function getEmailRecipients($workOrder): array
    {
        $recipients = [];

        if ($workOrder->assignedTo) {
            $recipients[] = $workOrder->assignedTo->email;
        }

        if (! $workOrder->assignedTo && $workOrder->clientAccount?->notification_email) {
            $recipients[] = $workOrder->clientAccount->notification_email;
        }

        if (empty($recipients) && $workOrder->reportedBy) {
            $recipients[] = $workOrder->reportedBy->email;
        }

        return array_unique(array_filter($recipients));
    }
}

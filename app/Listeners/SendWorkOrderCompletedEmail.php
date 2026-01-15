<?php

namespace App\Listeners;

use App\Events\WorkOrderCompleted;
use App\Mail\WorkOrderCompletedMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderCompletedEmail
{
    public function handle(WorkOrderCompleted $event): void
    {
        $workOrder = $event->workOrder->load(['completedBy']);

        // Send to users with closing authority
        // For now, send to facility managers or admins
        $recipients = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'manager']);
        })->get();

        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)
                ->send(new WorkOrderCompletedMail($workOrder));
        }
    }
}

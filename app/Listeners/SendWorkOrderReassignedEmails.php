<?php

namespace App\Listeners;

use App\Events\WorkOrderReassigned;
use App\Mail\WorkOrderReassignedFromPreviousMail;
use App\Mail\WorkOrderReassignedToNewMail;
use App\Notifications\WorkOrderAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendWorkOrderReassignedEmails implements ShouldQueue
{
    public function handle(WorkOrderReassigned $event): void
    {
        // Send in-app notification to new assignee
        $event->newAssignee->notify(
            new WorkOrderAssignedNotification($event->workOrder, isReassignment: true)
        );

        // Send email to the new assignee
        Mail::to($event->newAssignee->email)
            ->queue(new WorkOrderReassignedToNewMail(
                $event->workOrder,
                $event->reassignedBy,
                $event->reason
            ));

        // Send email to the previous assignee (if there was one)
        if ($event->previousAssignee) {
            Mail::to($event->previousAssignee->email)
                ->queue(new WorkOrderReassignedFromPreviousMail(
                    $event->workOrder,
                    $event->newAssignee,
                    $event->reassignedBy,
                    $event->reason
                ));
        }
    }
}

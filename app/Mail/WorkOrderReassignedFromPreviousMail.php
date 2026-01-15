<?php

namespace App\Mail;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkOrderReassignedFromPreviousMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public WorkOrder $workOrder,
        public User $newAssignee,
        public User $reassignedBy,
        public ?string $reason = null
    ) {
        $this->workOrder->loadMissing(['facility']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Work Order Reassigned: {$this->workOrder->workorder_serial} - {$this->workOrder->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.work-orders.reassigned-from-previous',
        );
    }
}

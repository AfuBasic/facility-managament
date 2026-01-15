<?php

namespace App\Mail;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkOrderReassignedToNewMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public WorkOrder $workOrder,
        public User $reassignedBy,
        public ?string $reason = null
    ) {
        $this->workOrder->loadMissing(['facility']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Work Order Assigned to You: {$this->workOrder->workorder_serial} - {$this->workOrder->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.work-orders.reassigned-to-new',
        );
    }
}

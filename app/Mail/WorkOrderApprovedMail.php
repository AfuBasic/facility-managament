<?php

namespace App\Mail;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkOrderApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WorkOrder $workOrder)
    {
        // Eager load relationships needed for the email template
        $this->workOrder->loadMissing(['reportedBy', 'approvedBy', 'facility']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Work Order Approved: {$this->workOrder->workorder_serial} - {$this->workOrder->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.work-orders.approved',
        );
    }
}

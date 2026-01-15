<?php

namespace App\Mail;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkOrderUpdateAddedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public WorkOrder $workOrder,
        public User $updatedBy
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Update on Work Order {$this->workOrder->workorder_serial}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.work-orders.update-added',
        );
    }
}

<?php

namespace App\Mail;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkOrderCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WorkOrder $workOrder)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Work Order Completed: #{$this->workOrder->id} - {$this->workOrder->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.work-orders.completed',
        );
    }
}

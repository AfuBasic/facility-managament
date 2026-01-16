<?php

namespace App\Mail;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SlaBreachedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public WorkOrder $workOrder,
        public string $breachType // 'response' or 'resolution'
    ) {
        $this->workOrder->loadMissing(['facility', 'assignedTo', 'reportedBy', 'slaPolicy']);
    }

    public function envelope(): Envelope
    {
        $typeLabel = $this->breachType === 'response' ? 'Response' : 'Resolution';

        return new Envelope(
            subject: "Urgent: SLA {$typeLabel} Breached: {$this->workOrder->workorder_serial}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.sla.breached',
        );
    }
}

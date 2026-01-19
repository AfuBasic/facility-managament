<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Contact;
use App\Services\IcsCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Event $event,
        public Contact $attendee
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Event Invitation: {$this->event->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.events.invitation',
        );
    }

    public function attachments(): array
    {
        $icsService = app(IcsCalendarService::class);
        $icsContent = $icsService->generate($this->event, $this->attendee);
        $filename = $icsService->getFilename($this->event);

        return [
            Attachment::fromData(fn () => $icsContent, $filename)
                ->withMime('text/plain'),
        ];
    }
}
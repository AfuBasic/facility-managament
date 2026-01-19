<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use App\Services\IcsCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Event $event,
        public User $attendee
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

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $icsService = app(IcsCalendarService::class);
        $icsContent = $icsService->generate($this->event, $this->attendee);
        $filename = $icsService->getFilename($this->event);

        return [
            Attachment::fromData(fn () => $icsContent, $filename)
                ->withMime('text/calendar'),
        ];
    }
}

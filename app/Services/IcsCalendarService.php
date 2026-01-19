<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Support\Str;

class IcsCalendarService
{
    /**
     * Generate ICS file content for an event.
     */
    public function generate(Event $event, ?Contact $attendee = null): string
    {
        $uid = uniqid('optima-', true).'@optimafm.com';
        $dtstamp = Carbon::now()->format('Ymd\THis\Z');
        $dtstart = $event->starts_at->format('Ymd\THis');
        $dtend = $event->ends_at
            ? $event->ends_at->format('Ymd\THis')
            : $event->starts_at->addHour()->format('Ymd\THis');

        $summary = $this->escapeIcsText($event->title);
        $description = $this->buildDescription($event);
        $location = $this->getLocation($event);

        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Optima FM//Event Calendar//EN\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:REQUEST\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:{$uid}\r\n";
        $ics .= "DTSTAMP:{$dtstamp}\r\n";
        $ics .= "DTSTART:{$dtstart}\r\n";
        $ics .= "DTEND:{$dtend}\r\n";
        $ics .= "SUMMARY:{$summary}\r\n";

        if ($description) {
            $ics .= "DESCRIPTION:{$description}\r\n";
        }

        if ($location) {
            $ics .= "LOCATION:{$location}\r\n";
        }

        // Add organizer if we have creator info
        if ($event->creator) {
            $organizerName = $this->escapeIcsText($event->creator->name);
            $organizerEmail = $event->creator->email;
            $ics .= "ORGANIZER;CN={$organizerName}:mailto:{$organizerEmail}\r\n";
        }

        // Add specific attendee if provided
        if ($attendee) {
            $attendeeName = $this->escapeIcsText($attendee->name);
            $attendeeEmail = $attendee->email;
            $ics .= "ATTENDEE;CN={$attendeeName};RSVP=TRUE:mailto:{$attendeeEmail}\r\n";
        } else {
            // Add all attendees
            foreach ($event->attendees as $eventAttendee) {
                $attendeeName = $this->escapeIcsText($eventAttendee->name);
                $attendeeEmail = $eventAttendee->email;
                $ics .= "ATTENDEE;CN={$attendeeName};RSVP=TRUE:mailto:{$attendeeEmail}\r\n";
            }
        }

        $ics .= "STATUS:CONFIRMED\r\n";
        $ics .= "SEQUENCE:0\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";

        return $ics;
    }

    /**
     * Build the description text for the ICS file.
     */
    protected function buildDescription(Event $event): string
    {
        $parts = [];

        if ($event->description) {
            $parts[] = $event->description;
        }

        if ($event->isVirtual() && $event->meeting_link) {
            $parts[] = "Join Meeting: {$event->meeting_link}";
        }

        if ($event->isPhysical() && $event->location) {
            $parts[] = "Location: {$event->location}";
        }

        return $this->escapeIcsText(implode('\n\n', $parts));
    }

    /**
     * Get the location string for the ICS file.
     */
    protected function getLocation(Event $event): ?string
    {
        if ($event->isVirtual() && $event->meeting_link) {
            return $this->escapeIcsText($event->meeting_link);
        }

        if ($event->isPhysical() && $event->location) {
            return $this->escapeIcsText($event->location);
        }

        return null;
    }

    /**
     * Escape special characters for ICS format.
     */
    protected function escapeIcsText(string $text): string
    {
        // Replace newlines with \n
        $text = str_replace(["\r\n", "\r", "\n"], '\n', $text);

        // Escape special characters
        $text = str_replace(['\\', ';', ','], ['\\\\', '\;', '\,'], $text);

        return $text;
    }

    /**
     * Get the filename for the ICS file.
     */
    public function getFilename(Event $event): string
    {
        $slug = Str::slug($event->title);

        return "event-{$slug}.txt";
    }
}

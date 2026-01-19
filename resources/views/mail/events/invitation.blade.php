<x-mail.layout title="Event Invitation" :greeting="'Hi ' . $attendee->name . ','">
    <p style="margin: 0 0 30px; color: #374151; font-size: 16px; line-height: 1.6;">
        You've been invited to an event hosted by <strong>{{ $event->clientAccount->name }}</strong>.
    </p>

    {{-- Event Card --}}
    <table role="presentation" style="width: 100%; background-color: #f9fafb; border-radius: 8px; border: 2px solid #e5e7eb; margin-bottom: 30px;">
        <tr>
            <td style="padding: 24px;">
                <h2 style="margin: 0 0 16px; color: #0d9488; font-size: 22px; font-weight: 600;">
                    {{ $event->title }}
                </h2>

                <table role="presentation" style="width: 100%;">
                    <x-mail.detail-row label="Date" :value="$event->formatted_date" />
                    <x-mail.detail-row label="Time" :value="$event->formatted_time" />
                    <x-mail.detail-row label="Type" :value="$event->isVirtual() ? 'Virtual Meeting' : 'Physical Meeting'" />

                    @if($event->isVirtual() && $event->meeting_link)
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; font-size: 14px; font-weight: 500; width: 120px; vertical-align: top;">
                            Join Meeting
                        </td>
                        <td style="padding: 8px 0; color: #374151; font-size: 14px;">
                            <a href="{{ $event->meeting_link }}" style="color: #0d9488; text-decoration: underline;">
                                {{ $event->meeting_link }}
                            </a>
                        </td>
                    </tr>
                    @endif

                    @if($event->isPhysical() && $event->location)
                    <x-mail.detail-row label="Location" :value="$event->location" />
                    @endif
                </table>

                @if($event->description)
                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                    <p style="margin: 0; color: #6b7280; font-size: 14px; font-weight: 500;">Description</p>
                    <p style="margin: 8px 0 0; color: #374151; font-size: 14px; line-height: 1.6;">
                        {{ $event->description }}
                    </p>
                </div>
                @endif
            </td>
        </tr>
    </table>

    <p style="margin: 0; color: #374151; font-size: 16px; line-height: 1.6;">
        Open the attached file to add this event to your calendar.
    </p>
</x-mail.layout>

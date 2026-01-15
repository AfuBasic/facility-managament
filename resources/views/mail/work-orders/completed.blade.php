<x-mail.layout title="Work Order Completed" :greeting="'Hi ' . $recipient->name . ','">
    <p style="margin: 0 0 30px; color: #374151; font-size: 16px; line-height: 1.6;">
        Great news! Your work order has been marked as completed by <strong>{{ $workOrder->completedBy->name }}</strong>.
    </p>

    {{-- Work Order Card --}}
    <table role="presentation" style="width: 100%; background-color: #f9fafb; border-radius: 8px; border: 2px solid #e5e7eb; margin-bottom: 30px;">
        <tr>
            <td style="padding: 24px;">
                <h2 style="margin: 0 0 16px; color: #0d9488; font-size: 22px; font-weight: 600;">
                    {{ $workOrder->title }}
                </h2>

                <table role="presentation" style="width: 100%;">
                    <x-mail.detail-row label="Work Order ID" :value="$workOrder->workorder_serial" />
                    <x-mail.detail-row label="Facility" :value="$workOrder->facility->name" />
                    <x-mail.detail-row label="Completed By" :value="$workOrder->completedBy->name" />
                    <x-mail.detail-row label="Completed" :value="$workOrder->completed_at->format('M d, Y g:i A')" />
                    @if($workOrder->completion_notes)
                    <x-mail.detail-row label="Notes" :value="$workOrder->completion_notes" />
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 10px; color: #374151; font-size: 16px; line-height: 1.6;">
        Thank you for your patience while we resolved this issue.
    </p>

    <x-mail.button :url="route('app.work-orders.show', $workOrder)" text="View Work Order" />
</x-mail.layout>

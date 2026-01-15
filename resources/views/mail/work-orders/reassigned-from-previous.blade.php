<x-mail.layout title="Work Order Reassigned" :greeting="'Hi there,'">
    <p style="margin: 0 0 30px; color: #374151; font-size: 16px; line-height: 1.6;">
        A work order previously assigned to you has been reassigned to <strong>{{ $newAssignee->name }}</strong> by <strong>{{ $reassignedBy->name }}</strong>.
    </p>

    {{-- Work Order Card --}}
    <table role="presentation" style="width: 100%; background-color: #f9fafb; border-radius: 8px; border: 2px solid #e5e7eb; margin-bottom: 30px;">
        <tr>
            <td style="padding: 24px;">
                <h2 style="margin: 0 0 16px; color: #6b7280; font-size: 22px; font-weight: 600;">
                    {{ $workOrder->title }}
                </h2>

                <table role="presentation" style="width: 100%;">
                    <x-mail.detail-row label="Work Order ID" :value="$workOrder->workorder_serial" />
                    <x-mail.detail-row label="Facility" :value="$workOrder->facility->name" />
                    <x-mail.detail-row label="Reassigned To" :value="$newAssignee->name" />
                    <x-mail.detail-row label="Reassigned By" :value="$reassignedBy->name" />
                    @if($reason)
                    <x-mail.detail-row label="Reason" :value="$reason" />
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 10px; color: #374151; font-size: 16px; line-height: 1.6;">
        You are no longer responsible for this work order. No further action is required from you.
    </p>

    <x-mail.button :url="route('app.work-orders.show', $workOrder)" text="View Work Order" />
</x-mail.layout>

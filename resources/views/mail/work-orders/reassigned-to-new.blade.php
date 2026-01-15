<x-mail.layout title="Work Order Assigned to You" :greeting="'Hi ' . $workOrder->assignedTo->name . ','">
    <p style="margin: 0 0 30px; color: #374151; font-size: 16px; line-height: 1.6;">
        A work order has been reassigned to you by <strong>{{ $reassignedBy->name }}</strong>. Please review the details below and take appropriate action.
    </p>

    {{-- Work Order Card --}}
    <table role="presentation" style="width: 100%; background-color: #f9fafb; border-radius: 8px; border: 2px solid #e5e7eb; margin-bottom: 30px;">
        <tr>
            <td style="padding: 24px;">
                <h2 style="margin: 0 0 16px; color: #0d9488; font-size: 22px; font-weight: 600;">
                    {{ $workOrder->title }}
                </h2>

                <p style="margin: 0 0 16px; color: #374151; font-size: 14px; line-height: 1.6;">
                    {{ $workOrder->description }}
                </p>

                <table role="presentation" style="width: 100%;">
                    <x-mail.detail-row label="Work Order ID" :value="$workOrder->workorder_serial" />
                    <x-mail.detail-row label="Facility" :value="$workOrder->facility->name" />
                    <x-mail.detail-row label="Priority" :value="ucfirst($workOrder->priority)" />
                    <x-mail.detail-row label="Status" :value="ucfirst(str_replace('_', ' ', $workOrder->status))" />
                    <x-mail.detail-row label="Reassigned By" :value="$reassignedBy->name" />
                    @if($reason)
                    <x-mail.detail-row label="Reason" :value="$reason" />
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 10px; color: #374151; font-size: 16px; line-height: 1.6;">
        Click the button below to view the full work order details:
    </p>

    <x-mail.button :url="route('app.work-orders.show', $workOrder)" text="View Work Order" />
</x-mail.layout>

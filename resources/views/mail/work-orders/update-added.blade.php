<x-mail.layout title="New Update" :greeting="'Hi there,'">
    <p style="margin: 0 0 30px; color: #374151; font-size: 16px; line-height: 1.6;">
        <strong>{{ $updatedBy->name }}</strong> has added an update to the work order.
    </p>

    {{-- Work Order Card --}}
    <table role="presentation" style="width: 100%; background-color: #f9fafb; border-radius: 8px; border: 2px solid #e5e7eb; margin-bottom: 30px;">
        <tr>
            <td style="padding: 24px;">
                <h2 style="margin: 0 0 16px; color: #0d9488; font-size: 22px; font-weight: 600;">
                    {{ $workOrder->title }}
                </h2>
                
                <table role="presentation" style="width: 100%;">
                    <x-mail.detail-row label="Work Order ID" :value="'#' . $workOrder->id" />
                    <x-mail.detail-row label="Facility" :value="$workOrder->facility->name" />
                    <x-mail.detail-row label="Status" :value="ucfirst(str_replace('_', ' ', $workOrder->status))" />
                    <x-mail.detail-row label="Updated By" :value="$updatedBy->name" />
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 10px; color: #374151; font-size: 16px; line-height: 1.6;">
        Check the work order to see the latest progress update.
    </p>

    <x-mail.button :url="route('app.work-orders.show', $workOrder)" text="View Work Order" />
</x-mail.layout>

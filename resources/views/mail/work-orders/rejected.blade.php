<x-mail.layout title="Work Order Rejected" :greeting="'Hi ' . $workOrder->reportedBy->name . ','">
    <p style="margin: 0 0 30px; color: #374151; font-size: 16px; line-height: 1.6;">
        Unfortunately, your work order has been rejected and will not proceed further.
    </p>

    {{-- Work Order Card --}}
    <table role="presentation" style="width: 100%; background-color: #f9fafb; border-radius: 8px; border: 2px solid #e5e7eb; margin-bottom: 30px;">
        <tr>
            <td style="padding: 24px;">
                <h2 style="margin: 0 0 16px; color: #dc2626; font-size: 22px; font-weight: 600;">
                    {{ $workOrder->title }}
                </h2>

                <table role="presentation" style="width: 100%;">
                    <x-mail.detail-row label="Work Order ID" :value="'#' . $workOrder->id" />
                    <x-mail.detail-row label="Facility" :value="$workOrder->facility->name" />
                    <x-mail.detail-row label="Priority" :value="ucfirst($workOrder->priority)" />
                    <x-mail.detail-row label="Rejected By" :value="$workOrder->rejectedBy->name" />
                    <x-mail.detail-row label="Rejected" :value="$workOrder->rejected_at->format('M d, Y g:i A')" />
                    @if($workOrder->rejection_reason)
                    <x-mail.detail-row label="Reason" :value="$workOrder->rejection_reason" />
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 10px; color: #374151; font-size: 16px; line-height: 1.6;">
        If you believe this was rejected in error, please contact your facility manager.
    </p>

    <x-mail.button :url="route('app.work-orders.show', $workOrder)" text="View Work Order" />
</x-mail.layout>

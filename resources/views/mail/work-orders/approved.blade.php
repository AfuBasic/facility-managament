<x-mail.layout title="Work Order Approved" :greeting="'Hi ' . $workOrder->reportedBy->name . ','">
    <p style="margin: 0 0 30px; color: #374151; font-size: 16px; line-height: 1.6;">
        Good news! Your work order has been approved and will be assigned to a technician shortly.
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
                    <x-mail.detail-row label="Priority" :value="ucfirst($workOrder->priority)" />
                    <x-mail.detail-row label="Approved By" :value="$workOrder->approvedBy->name" />
                    @if($workOrder->approval_note)
                    <x-mail.detail-row label="Note" :value="$workOrder->approval_note" />
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 10px; color: #374151; font-size: 16px; line-height: 1.6;">
        You'll receive another notification once a technician is assigned.
    </p>

    <x-mail.button :url="route('app.work-orders.show', $workOrder)" text="View Work Order" />
</x-mail.layout>

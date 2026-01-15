<x-mail.layout title="Work Started" :greeting="'Hi ' . $workOrder->reportedBy->name . ','">
    <p style="margin: 0 0 30px; color: #374151; font-size: 16px; line-height: 1.6;">
        Good news! Work has been started on your work order.
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
                    <x-mail.detail-row label="Assigned To" :value="$workOrder->assignedTo->name" />
                    <x-mail.detail-row label="Started" :value="$workOrder->started_at->format('M d, Y g:i A')" />
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 10px; color: #374151; font-size: 16px; line-height: 1.6;">
        The assigned technician has begun working on this issue. You'll receive updates as progress is made.
    </p>

    <x-mail.button :url="route('app.work-orders.show', $workOrder)" text="View Work Order" />
</x-mail.layout>

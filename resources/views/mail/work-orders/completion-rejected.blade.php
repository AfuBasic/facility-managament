<x-mail.layout title="Completion Rejected" :greeting="'Hi ' . $workOrder->assignedTo->name . ','">
    <p style="margin: 0 0 30px; color: #374151; font-size: 16px; line-height: 1.6;">
        The completion of your work order has been rejected by <strong>{{ $rejectedBy->name }}</strong>. Please review the feedback and address the issues.
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
                    <x-mail.detail-row label="Rejected By" :value="$rejectedBy->name" />
                    <x-mail.detail-row label="Rejection Reason" :value="$reason" />
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 10px; color: #374151; font-size: 16px; line-height: 1.6;">
        The work order has been moved back to "In Progress". Please address the issues and mark as complete again when ready.
    </p>

    <x-mail.button :url="route('app.work-orders.show', $workOrder)" text="View Work Order" />
</x-mail.layout>

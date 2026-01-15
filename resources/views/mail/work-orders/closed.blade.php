<x-mail.layout title="Work Order Closed" :greeting="'Hi there,'">
    <p style="margin: 0 0 30px; color: #374151; font-size: 16px; line-height: 1.6;">
        The work order has been closed and archived. This issue is now resolved.
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
                    <x-mail.detail-row label="Closed By" :value="$workOrder->closedBy->name" />
                    <x-mail.detail-row label="Closed" :value="$workOrder->closed_at->format('M d, Y g:i A')" />
                    @if($workOrder->closure_note)
                    <x-mail.detail-row label="Note" :value="$workOrder->closure_note" />
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

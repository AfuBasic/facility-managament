<x-mail.layout 
    :title="$breachType === 'response' ? 'Response SLA Breached' : 'Resolution SLA Breached'" 
    :greeting="'Hi ' . ($workOrder->assignedTo?->name ?? 'there') . ','">
    
    <p style="margin: 0 0 30px; color: #374151; font-size: 16px; line-height: 1.6;">
        @if($breachType === 'response')
            <strong>⚠️ Response SLA has been breached</strong> for the following work order. No response was recorded before the deadline.
        @else
            <strong>⚠️ Resolution SLA has been breached</strong> for the following work order. The work was not completed before the deadline.
        @endif
    </p>

    {{-- Work Order Card --}}
    <table role="presentation" style="width: 100%; background-color: #fef2f2; border-radius: 8px; border: 2px solid #fecaca; margin-bottom: 30px;">
        <tr>
            <td style="padding: 24px;">
                <h2 style="margin: 0 0 16px; color: #dc2626; font-size: 22px; font-weight: 600;">
                    {{ $workOrder->title }}
                </h2>
                
                <table role="presentation" style="width: 100%;">
                    <x-mail.detail-row label="Work Order ID" :value="$workOrder->workorder_serial" />
                    <x-mail.detail-row label="Facility" :value="$workOrder->facility->name" />
                    <x-mail.detail-row label="Priority" :value="ucfirst($workOrder->priority)" />
                    <x-mail.detail-row label="Status" :value="ucfirst(str_replace('_', ' ', $workOrder->status))" />
                    @if($breachType === 'response')
                        <x-mail.detail-row label="Response Due" :value="$workOrder->response_due_at?->format('M d, Y g:i A') ?? 'N/A'" />
                        <x-mail.detail-row label="Breached At" :value="$workOrder->sla_response_breached_at?->format('M d, Y g:i A') ?? now()->format('M d, Y g:i A')" />
                    @else
                        <x-mail.detail-row label="Resolution Due" :value="$workOrder->resolution_due_at?->format('M d, Y g:i A') ?? 'N/A'" />
                        <x-mail.detail-row label="Breached At" :value="$workOrder->sla_resolution_breached_at?->format('M d, Y g:i A') ?? now()->format('M d, Y g:i A')" />
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 10px; color: #374151; font-size: 16px; line-height: 1.6;">
        Please take immediate action to address this work order.
    </p>

    <x-mail.button :url="route('app.work-orders.show', $workOrder)" text="View Work Order" />
</x-mail.layout>

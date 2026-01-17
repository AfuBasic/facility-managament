<?php

namespace App\Notifications;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SlaBreachedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WorkOrder $workOrder,
        public string $breachType // 'response' or 'resolution'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $typeLabel = $this->breachType === 'response' ? 'Response' : 'Resolution';

        return [
            'work_order_id' => $this->workOrder->id,
            'work_order_serial' => $this->workOrder->workorder_serial,
            'title' => $this->workOrder->title,
            'facility_name' => $this->workOrder->facility?->name,
            'breach_type' => $this->breachType,
            'message' => "SLA {$typeLabel} breached for work order {$this->workOrder->workorder_serial}",
            'icon' => 'exclamation-triangle',
            'color' => 'red',
            'route' => route('app.work-orders.show', $this->workOrder),
            'route_name' => 'View Work Order',
        ];
    }
}

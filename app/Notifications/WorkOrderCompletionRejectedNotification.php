<?php

namespace App\Notifications;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkOrderCompletionRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WorkOrder $workOrder,
        public string $reason
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'work_order_id' => $this->workOrder->id,
            'work_order_serial' => $this->workOrder->workorder_serial,
            'title' => $this->workOrder->title,
            'facility_name' => $this->workOrder->facility?->name,
            'reason' => $this->reason,
            'message' => "Work order {$this->workOrder->workorder_serial} completion was rejected: {$this->reason}",
            'icon' => 'x-circle',
            'color' => 'red',
            'route' => route('app.work-orders.show', $this->workOrder),
            'route_name' => 'View Work Order',
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkOrderRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(public WorkOrder $workOrder) {}

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
            'message' => "Work order {$this->workOrder->workorder_serial} has been rejected".($this->workOrder->rejectedBy ? " by {$this->workOrder->rejectedBy->name}" : ''),
            'icon' => 'x-circle',
            'color' => 'red',
            'route' => route('app.work-orders.show', $this->workOrder),
            'route_name' => 'View Work Order',
        ];
    }
}

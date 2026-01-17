<?php

namespace App\Notifications;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkOrderClosedNotification extends Notification
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
            'message' => "Work order {$this->workOrder->workorder_serial} has been closed".($this->workOrder->closedBy ? " by {$this->workOrder->closedBy->name}" : ''),
            'icon' => 'archive-box',
            'color' => 'slate',
            'route' => route('app.work-orders.show', $this->workOrder),
            'route_name' => 'View Work Order',
        ];
    }
}

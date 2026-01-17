<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkOrderUpdateAddedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WorkOrder $workOrder,
        public User $updatedBy
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
            'message' => "{$this->updatedBy->name} added an update to work order {$this->workOrder->workorder_serial}",
            'icon' => 'chat-bubble-left-ellipsis',
            'color' => 'blue',
            'route' => route('app.work-orders.show', $this->workOrder),
            'route_name' => 'View Work Order',
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkOrderAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WorkOrder $workOrder,
        public bool $isReassignment = false
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
            'priority' => $this->workOrder->priority,
            'is_reassignment' => $this->isReassignment,
            'message' => $this->isReassignment
                ? "Work order {$this->workOrder->workorder_serial} has been reassigned to you"
                : "You have been assigned work order {$this->workOrder->workorder_serial}",
            'icon' => 'clipboard-document-list',
            'color' => 'teal',
            'route' => route('app.work-orders.show', $this->workOrder),
            'route_name' => 'View Work Order',
        ];
    }
}

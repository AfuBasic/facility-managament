<?php

namespace App\Notifications;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkOrderStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WorkOrder $workOrder,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statusLabels = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'on_hold' => 'On Hold',
            'done' => 'Done',
            'completed' => 'Completed',
            'closed' => 'Closed',
            'rejected' => 'Rejected',
        ];

        $newLabel = $statusLabels[$this->newStatus] ?? ucfirst($this->newStatus);

        return [
            'work_order_id' => $this->workOrder->id,
            'work_order_serial' => $this->workOrder->workorder_serial,
            'title' => $this->workOrder->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "Work order {$this->workOrder->workorder_serial} status changed to {$newLabel}",
            'icon' => 'arrow-path',
            'color' => 'blue',
            'route' => route('app.work-orders.show', $this->workOrder),
            'route_name' => 'View Work Order',
        ];
    }
}

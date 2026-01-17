<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkOrderPausedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WorkOrder $workOrder,
        public User $pausedBy,
        public ?string $reason = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = "Work order {$this->workOrder->workorder_serial} has been paused by {$this->pausedBy->name}";
        if ($this->reason) {
            $message .= ": {$this->reason}";
        }

        return [
            'work_order_id' => $this->workOrder->id,
            'work_order_serial' => $this->workOrder->workorder_serial,
            'title' => $this->workOrder->title,
            'facility_name' => $this->workOrder->facility?->name,
            'reason' => $this->reason,
            'message' => $message,
            'icon' => 'pause-circle',
            'color' => 'amber',
            'route' => route('app.work-orders.show', $this->workOrder),
            'route_name' => 'View Work Order',
        ];
    }
}

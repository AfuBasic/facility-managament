<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkOrderReopenedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WorkOrder $workOrder,
        public User $reopenedBy,
        public ?string $reason = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = "Work order {$this->workOrder->workorder_serial} has been reopened by {$this->reopenedBy->name}";
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
            'icon' => 'arrow-path-rounded-square',
            'color' => 'blue',
            'route' => route('app.work-orders.show', $this->workOrder),
            'route_name' => 'View Work Order',
        ];
    }
}

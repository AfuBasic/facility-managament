<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkOrderResumedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WorkOrder $workOrder,
        public User $resumedBy
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
            'message' => "Work order {$this->workOrder->workorder_serial} has been resumed by {$this->resumedBy->name}",
            'icon' => 'play-circle',
            'color' => 'teal',
            'route' => route('app.work-orders.show', ['workOrder' => $this->workOrder, 'tab' => 'history']),
            'route_name' => 'View History',
        ];
    }
}

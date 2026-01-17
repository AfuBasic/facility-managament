<?php

namespace App\Notifications;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkorderStartedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public WorkOrder $workOrder) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'work_order_id' => $this->workOrder->id,
            'work_order_serial' => $this->workOrder->workorder_serial,
            'title' => $this->workOrder->title,
            'facility_name' => $this->workOrder->facility?->name,
            'priority' => $this->workOrder->priority,
            'message' => "Work order {$this->workOrder->workorder_serial} has been started".($this->workOrder->started_by ? " by {$this->workOrder->startedBy->name}" : ''),
            'icon' => 'clipboard-document-list',
            'color' => 'teal',
            'route' => route('app.work-orders.show', $this->workOrder),
            'route_name' => 'View Work Order',
        ];
    }
}

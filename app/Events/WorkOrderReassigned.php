<?php

namespace App\Events;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkOrderReassigned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public WorkOrder $workOrder,
        public ?User $previousAssignee,
        public User $newAssignee,
        public User $reassignedBy,
        public ?string $reason = null
    ) {}
}

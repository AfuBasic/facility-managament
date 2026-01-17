<?php

namespace App\Events;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkOrderReopened
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public WorkOrder $workOrder,
        public User $reopenedBy,
        public ?string $reason = null
    ) {}
}

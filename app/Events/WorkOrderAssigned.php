<?php

namespace App\Events;

use App\Models\WorkOrder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkOrderAssigned
{
    use Dispatchable, SerializesModels;

    public function __construct(public WorkOrder $workOrder)
    {
        //
    }
}

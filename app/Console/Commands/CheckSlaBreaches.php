<?php

namespace App\Console\Commands;

use App\Events\SlaResolutionBreached;
use App\Events\SlaResponseBreached;
use App\Models\WorkOrder;
use App\Services\SlaCalculatorService;
use Illuminate\Console\Command;

class CheckSlaBreaches extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sla:check-breaches';

    /**
     * The console command description.
     */
    protected $description = 'Check for SLA breaches and trigger notifications';

    public function __construct(protected SlaCalculatorService $slaCalculator)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for SLA breaches...');

        $responseBreaches = $this->checkResponseBreaches();
        $resolutionBreaches = $this->checkResolutionBreaches();

        $this->info("Response breaches detected: {$responseBreaches}");
        $this->info("Resolution breaches detected: {$resolutionBreaches}");

        return self::SUCCESS;
    }

    /**
     * Check for response SLA breaches.
     */
    protected function checkResponseBreaches(): int
    {
        $count = 0;

        // Find work orders that have response deadline passed but not yet marked as breached
        $workOrders = WorkOrder::whereNotNull('response_due_at')
            ->whereNull('responded_at')
            ->where('sla_response_breached', false)
            ->where('response_due_at', '<', now())
            ->whereNotIn('status', ['completed', 'closed', 'rejected'])
            ->get();

        foreach ($workOrders as $workOrder) {
            $workOrder->update([
                'sla_response_breached' => true,
                'sla_response_breached_at' => now(),
            ]);

            SlaResponseBreached::dispatch($workOrder);
            $count++;
        }

        return $count;
    }

    /**
     * Check for resolution SLA breaches.
     */
    protected function checkResolutionBreaches(): int
    {
        $count = 0;

        // Find work orders that have resolution deadline passed but not yet marked as breached
        $workOrders = WorkOrder::whereNotNull('resolution_due_at')
            ->where('sla_resolution_breached', false)
            ->where('resolution_due_at', '<', now())
            ->whereNotIn('status', ['completed', 'closed', 'rejected'])
            ->get();

        foreach ($workOrders as $workOrder) {
            $workOrder->update([
                'sla_resolution_breached' => true,
                'sla_resolution_breached_at' => now(),
            ]);

            SlaResolutionBreached::dispatch($workOrder);
            $count++;
        }

        return $count;
    }
}

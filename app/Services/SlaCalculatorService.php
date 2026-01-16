<?php

namespace App\Services;

use App\Models\BusinessHours;
use App\Models\ClientAccount;
use App\Models\SlaPolicy;
use App\Models\WorkOrder;
use Carbon\Carbon;

class SlaCalculatorService
{
    /**
     * Apply SLA policy to a work order and calculate deadlines.
     */
    public function applyPolicy(WorkOrder $workOrder): void
    {
        $policy = $this->getApplicablePolicy($workOrder);

        if (! $policy) {
            return;
        }

        $rule = $policy->getRuleForPriority($workOrder->priority);

        if (! $rule) {
            return;
        }

        $startTime = $workOrder->reported_at ?? now();

        $responseDeadline = $policy->business_hours_only
            ? $this->addBusinessMinutes($startTime, $rule->response_time_minutes, $workOrder->clientAccount)
            : $startTime->copy()->addMinutes($rule->response_time_minutes);

        $resolutionDeadline = $policy->business_hours_only
            ? $this->addBusinessMinutes($startTime, $rule->resolution_time_minutes, $workOrder->clientAccount)
            : $startTime->copy()->addMinutes($rule->resolution_time_minutes);

        $workOrder->update([
            'sla_policy_id' => $policy->id,
            'response_due_at' => $responseDeadline,
            'resolution_due_at' => $resolutionDeadline,
        ]);
    }

    /**
     * Get the applicable SLA policy for a work order.
     */
    protected function getApplicablePolicy(WorkOrder $workOrder): ?SlaPolicy
    {
        // First check if work order already has a policy assigned
        if ($workOrder->sla_policy_id) {
            return $workOrder->slaPolicy;
        }

        // Get default policy for client
        return SlaPolicy::getDefaultForClient($workOrder->clientAccount);
    }

    /**
     * Check if response SLA is breached.
     */
    public function isResponseBreached(WorkOrder $workOrder): bool
    {
        if (! $workOrder->response_due_at) {
            return false;
        }

        // If already responded, check if it was on time
        if ($workOrder->responded_at) {
            return $workOrder->responded_at->gt($workOrder->response_due_at);
        }

        // Not responded yet, check if deadline passed
        return now()->gt($workOrder->response_due_at);
    }

    /**
     * Check if resolution SLA is breached.
     */
    public function isResolutionBreached(WorkOrder $workOrder): bool
    {
        if (! $workOrder->resolution_due_at) {
            return false;
        }

        // If completed, check if it was on time
        if ($workOrder->completed_at) {
            return $workOrder->completed_at->gt($workOrder->resolution_due_at);
        }

        // If closed, it's not breached
        if ($workOrder->status === 'closed') {
            return false;
        }

        // Not completed yet, check if deadline passed
        return now()->gt($workOrder->resolution_due_at);
    }

    /**
     * Get remaining time until deadline in minutes.
     * Returns null if deadline is not set, negative if breached.
     */
    public function getTimeRemaining(Carbon $deadline): ?int
    {
        return (int) now()->diffInMinutes($deadline, false);
    }

    /**
     * Get human-readable remaining time.
     */
    public function getTimeRemainingHuman(Carbon $deadline): string
    {
        $minutes = $this->getTimeRemaining($deadline);

        if ($minutes < 0) {
            return 'Breached '.$this->formatMinutes(abs($minutes)).' ago';
        }

        if ($minutes < 60) {
            return $minutes.' min'.($minutes > 1 ? 's' : '').' remaining';
        }

        return $this->formatMinutes($minutes).' remaining';
    }

    /**
     * Add business minutes to a timestamp.
     */
    public function addBusinessMinutes(Carbon $start, int $minutes, ClientAccount $clientAccount): Carbon
    {
        $businessHours = BusinessHours::getForClient($clientAccount);

        // If no business hours defined, use 24/7
        if (empty($businessHours) || ! $this->hasAnyBusinessHours($businessHours)) {
            return $start->copy()->addMinutes($minutes);
        }

        $current = $start->copy();
        $remainingMinutes = $minutes;

        // Safety limit to prevent infinite loops
        $maxIterations = 365 * 24; // One year of hours
        $iterations = 0;

        while ($remainingMinutes > 0 && $iterations < $maxIterations) {
            $iterations++;
            $dayOfWeek = $current->dayOfWeek;
            $dayHours = $businessHours[$dayOfWeek] ?? null;

            // Skip if day is closed
            if (! $dayHours || $dayHours->is_closed) {
                $current->addDay()->startOfDay();

                continue;
            }

            $openTime = Carbon::parse($dayHours->open_time, $current->timezone)->setDate($current->year, $current->month, $current->day);
            $closeTime = Carbon::parse($dayHours->close_time, $current->timezone)->setDate($current->year, $current->month, $current->day);

            // If current time is before business hours, move to open time
            if ($current->lt($openTime)) {
                $current = $openTime->copy();
            }

            // If current time is after business hours, move to next day
            if ($current->gte($closeTime)) {
                $current->addDay()->startOfDay();

                continue;
            }

            // Calculate available minutes today
            $availableMinutes = (int) $current->diffInMinutes($closeTime);

            if ($availableMinutes >= $remainingMinutes) {
                return $current->addMinutes($remainingMinutes);
            }

            $remainingMinutes -= $availableMinutes;
            $current->addDay()->startOfDay();
        }

        return $current;
    }

    /**
     * Check if any business hours are defined.
     */
    protected function hasAnyBusinessHours(array $businessHours): bool
    {
        foreach ($businessHours as $dayHours) {
            if ($dayHours && ! $dayHours->is_closed) {
                return true;
            }
        }

        return false;
    }

    /**
     * Format minutes into human-readable string.
     */
    protected function formatMinutes(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} min".($minutes > 1 ? 's' : '');
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours < 24) {
            return "{$hours}h".($remainingMinutes > 0 ? " {$remainingMinutes}m" : '');
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        return "{$days}d".($remainingHours > 0 ? " {$remainingHours}h" : '');
    }

    /**
     * Get SLA status for display.
     */
    public function getSlaStatus(WorkOrder $workOrder): array
    {
        // If no SLA policy, return null status
        if (! $workOrder->sla_policy_id) {
            return [
                'status' => 'none',
                'label' => 'No SLA',
                'variant' => 'neutral',
            ];
        }

        // Check for breaches
        if ($workOrder->sla_response_breached || $workOrder->sla_resolution_breached) {
            return [
                'status' => 'breached',
                'label' => 'SLA Breached',
                'variant' => 'danger',
            ];
        }

        // Check if at risk (< 30 mins remaining)
        $responseRemaining = $workOrder->response_due_at && ! $workOrder->responded_at
            ? $this->getTimeRemaining($workOrder->response_due_at)
            : null;

        $resolutionRemaining = $workOrder->resolution_due_at && ! in_array($workOrder->status, ['completed', 'closed'])
            ? $this->getTimeRemaining($workOrder->resolution_due_at)
            : null;

        // If either deadline is at risk
        if (($responseRemaining !== null && $responseRemaining <= 30 && $responseRemaining > 0) ||
            ($resolutionRemaining !== null && $resolutionRemaining <= 30 && $resolutionRemaining > 0)) {
            return [
                'status' => 'at_risk',
                'label' => 'At Risk',
                'variant' => 'warning',
            ];
        }

        // Check if overdue but not yet marked as breached
        if (($responseRemaining !== null && $responseRemaining < 0) ||
            ($resolutionRemaining !== null && $resolutionRemaining < 0)) {
            return [
                'status' => 'breached',
                'label' => 'SLA Breached',
                'variant' => 'danger',
            ];
        }

        return [
            'status' => 'on_track',
            'label' => 'On Track',
            'variant' => 'success',
        ];
    }
}

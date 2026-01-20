<?php

namespace App\Livewire\Concerns;

use Carbon\Carbon;
use Livewire\Attributes\Url;

trait HasDateRangeFilter
{
    #[Url]
    public string $dateRange = 'last_30_days';

    #[Url]
    public ?string $startDate = null;

    #[Url]
    public ?string $endDate = null;

    public function getDateRangePresets(): array
    {
        return [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'last_7_days' => 'Last 7 Days',
            'last_30_days' => 'Last 30 Days',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_quarter' => 'This Quarter',
            'last_quarter' => 'Last Quarter',
            'this_year' => 'This Year',
            'last_year' => 'Last Year',
            'custom' => 'Custom Range',
        ];
    }

    public function getDateRange(): array
    {
        if ($this->dateRange === 'custom' && $this->startDate && $this->endDate) {
            return [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ];
        }

        return match ($this->dateRange) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'last_7_days' => [now()->subDays(7)->startOfDay(), now()->endOfDay()],
            'last_30_days' => [now()->subDays(30)->startOfDay(), now()->endOfDay()],
            'this_month' => [now()->startOfMonth(), now()->endOfMonth()],
            'last_month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            'last_quarter' => [now()->subQuarter()->startOfQuarter(), now()->subQuarter()->endOfQuarter()],
            'this_year' => [now()->startOfYear(), now()->endOfYear()],
            'last_year' => [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()],
            default => [now()->subDays(30)->startOfDay(), now()->endOfDay()],
        };
    }

    public function getDateRangeLabel(): string
    {
        if ($this->dateRange === 'custom' && $this->startDate && $this->endDate) {
            return Carbon::parse($this->startDate)->format('M d, Y').' - '.Carbon::parse($this->endDate)->format('M d, Y');
        }

        return $this->getDateRangePresets()[$this->dateRange] ?? 'Last 30 Days';
    }

    public function updatedDateRange(): void
    {
        if ($this->dateRange !== 'custom') {
            $this->startDate = null;
            $this->endDate = null;
        }
    }
}

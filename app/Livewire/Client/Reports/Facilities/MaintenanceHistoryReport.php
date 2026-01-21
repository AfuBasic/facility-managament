<?php

namespace App\Livewire\Client\Reports\Facilities;

use App\Exports\MaintenanceHistoryExport;
use App\Livewire\Concerns\HasDateRangeFilter;
use App\Models\ClientAccount;
use App\Models\Facility;
use App\Models\WorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.client-app')]
#[Title('Maintenance History Report | Optima FM')]
class MaintenanceHistoryReport extends Component
{
    use HasDateRangeFilter;

    public ClientAccount $clientAccount;

    public ?int $facilityId = null;

    public function mount(): void
    {
        $this->authorize('view reports');
        $this->clientAccount = app(ClientAccount::class);
    }

    protected function getClientId(): int
    {
        return $this->clientAccount->id ?? app(ClientAccount::class)->id;
    }

    public function getFacilitiesProperty()
    {
        return Facility::where('client_account_id', $this->getClientId())
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function getReportData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $query = WorkOrder::where('client_account_id', $this->getClientId())
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($this->facilityId) {
            $query->where('facility_id', $this->facilityId);
        }

        // Per-Facility Summary
        $facilityData = WorkOrder::where('work_orders.client_account_id', $this->getClientId())
            ->whereBetween('work_orders.created_at', [$startDate, $endDate])
            ->when($this->facilityId, fn ($q) => $q->where('work_orders.facility_id', $this->facilityId))
            ->join('facilities', 'work_orders.facility_id', '=', 'facilities.id')
            ->select(
                'facilities.id',
                'facilities.name',
                DB::raw('COUNT(work_orders.id) as total_orders'),
                DB::raw('SUM(CASE WHEN work_orders.status IN ("completed", "closed") THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN work_orders.status IN ("reported", "approved", "assigned", "in_progress") THEN 1 ELSE 0 END) as open'),
                DB::raw('COALESCE(SUM(work_orders.total_cost), 0) as total_cost'),
                DB::raw('AVG(CASE WHEN work_orders.completed_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR, work_orders.created_at, work_orders.completed_at) ELSE NULL END) as avg_completion_hours')
            )
            ->groupBy('facilities.id', 'facilities.name')
            ->orderBy('total_orders', 'desc')
            ->get()
            ->map(fn ($f) => [
                'id' => $f->id,
                'name' => $f->name,
                'total_orders' => $f->total_orders,
                'completed' => $f->completed,
                'open' => $f->open,
                'total_cost' => number_format($f->total_cost, 2),
                'avg_completion_hours' => $f->avg_completion_hours ? round($f->avg_completion_hours, 1) : '-',
            ])
            ->toArray();

        // Monthly Trend
        $trendData = WorkOrder::where('client_account_id', $this->getClientId())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($this->facilityId, fn ($q) => $q->where('facility_id', $this->facilityId))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('COALESCE(SUM(total_cost), 0) as cost')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($item) => [
                'label' => \Carbon\Carbon::parse($item->month.'-01')->format('M Y'),
                'count' => $item->count,
                'cost' => $item->cost,
            ])
            ->toArray();

        // Summary Stats
        $totals = $query->clone()->select(
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('COALESCE(SUM(total_cost), 0) as total_cost'),
            DB::raw('SUM(CASE WHEN status IN ("completed", "closed") THEN 1 ELSE 0 END) as completed'),
            DB::raw('AVG(CASE WHEN completed_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR, created_at, completed_at) ELSE NULL END) as avg_hours')
        )->first();

        return [
            'facilityData' => $facilityData,
            'trendData' => $trendData,
            'summary' => [
                'total_orders' => $totals->total_orders ?? 0,
                'total_cost' => number_format($totals->total_cost ?? 0, 2),
                'completed' => $totals->completed ?? 0,
                'avg_hours' => $totals->avg_hours ? round($totals->avg_hours, 1) : 0,
                'facilities_count' => count($facilityData),
            ],
        ];
    }

    public function exportPdf()
    {
        $data = $this->getReportData();

        $pdf = Pdf::loadView('exports.pdf.reports.maintenance-history', [
            'data' => $data,
            'title' => 'Maintenance History Report',
            'dateRange' => $this->getDateRangeLabel(),
            'generatedAt' => now(),
            'clientName' => $this->clientAccount->name,
            'currency' => $this->clientAccount->getCurrencySymbol(),
        ]);

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'maintenance-history-report-'.now()->format('Y-m-d').'.pdf'
        );
    }

    public function exportExcel()
    {
        $data = $this->getReportData();

        return Excel::download(
            new MaintenanceHistoryExport($data, $this->getDateRangeLabel()),
            'maintenance-history-report-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.client.reports.facilities.maintenance-history', [
            'data' => $this->getReportData(),
        ]);
    }
}

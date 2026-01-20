<?php

namespace App\Livewire\Client\Reports\Financial;

use App\Exports\CostSummaryExport;
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
#[Title('Cost Summary Report | Optima FM')]
class CostSummaryReport extends Component
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

        $baseQuery = WorkOrder::where('client_account_id', $this->getClientId())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($this->facilityId, fn($q) => $q->where('facility_id', $this->facilityId));

        // Cost by Facility
        $costByFacility = WorkOrder::where('work_orders.client_account_id', $this->getClientId())
            ->whereBetween('work_orders.created_at', [$startDate, $endDate])
            ->when($this->facilityId, fn($q) => $q->where('facility_id', $this->facilityId))
            ->leftJoin('facilities', 'work_orders.facility_id', '=', 'facilities.id')
            ->select(
                'facilities.name as facility_name',
                DB::raw('COUNT(work_orders.id) as order_count'),
                DB::raw('COALESCE(SUM(work_orders.total_cost), 0) as total_cost'),
                DB::raw('COALESCE(AVG(work_orders.total_cost), 0) as avg_cost')
            )
            ->groupBy('facilities.id', 'facilities.name')
            ->orderBy('total_cost', 'desc')
            ->get()
            ->map(fn($f) => [
                'facility' => $f->facility_name ?: 'Unassigned',
                'order_count' => $f->order_count,
                'total_cost' => number_format($f->total_cost, 2),
                'avg_cost' => number_format($f->avg_cost, 2),
            ])
            ->toArray();

        // Cost by Priority
        $costByPriority = $baseQuery->clone()
            ->select(
                'priority',
                DB::raw('COUNT(*) as count'),
                DB::raw('COALESCE(SUM(total_cost), 0) as total_cost')
            )
            ->groupBy('priority')
            ->orderBy('total_cost', 'desc')
            ->get()
            ->map(fn($p) => [
                'priority' => ucfirst($p->priority),
                'count' => $p->count,
                'total_cost' => number_format($p->total_cost, 2),
            ])
            ->toArray();

        // Monthly Cost Trend
        $monthlyTrend = $baseQuery->clone()
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('COALESCE(SUM(total_cost), 0) as total_cost')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($item) => [
                'label' => \Carbon\Carbon::parse($item->month . '-01')->format('M Y'),
                'count' => $item->count,
                'cost' => $item->total_cost,
            ])
            ->toArray();

        // Top 10 Costly Work Orders
        $topCostlyOrders = $baseQuery->clone()
            ->whereNotNull('total_cost')
            ->where('total_cost', '>', 0)
            ->with(['facility', 'reportedBy'])
            ->orderBy('total_cost', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($wo) => [
                'id' => $wo->id,
                'serial' => $wo->workorder_serial,
                'title' => $wo->title,
                'facility' => $wo->facility?->name ?? 'N/A',
                'status' => ucfirst(str_replace('_', ' ', $wo->status)),
                'priority' => ucfirst($wo->priority),
                'cost' => number_format($wo->total_cost, 2),
                'created_at' => $wo->created_at->format('M d, Y'),
            ])
            ->toArray();

        // Summary Stats
        $summary = $baseQuery->clone()->select(
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('COALESCE(SUM(total_cost), 0) as total_cost'),
            DB::raw('COALESCE(AVG(total_cost), 0) as avg_cost'),
            DB::raw('COALESCE(MAX(total_cost), 0) as max_cost')
        )->first();

        return [
            'costByFacility' => $costByFacility,
            'costByPriority' => $costByPriority,
            'monthlyTrend' => $monthlyTrend,
            'topCostlyOrders' => $topCostlyOrders,
            'summary' => [
                'total_orders' => $summary->total_orders ?? 0,
                'total_cost' => number_format($summary->total_cost ?? 0, 2),
                'avg_cost' => number_format($summary->avg_cost ?? 0, 2),
                'max_cost' => number_format($summary->max_cost ?? 0, 2),
            ],
        ];
    }

    public function exportPdf()
    {
        $data = $this->getReportData();

        $pdf = Pdf::loadView('exports.pdf.reports.cost-summary', [
            'data' => $data,
            'title' => 'Cost Summary Report',
            'dateRange' => $this->getDateRangeLabel(),
            'generatedAt' => now(),
            'clientName' => $this->clientAccount->name,
        ]);

        return response()->streamDownload(
            fn() => print($pdf->output()),
            'cost-summary-report-' . now()->format('Y-m-d') . '.pdf'
        );
    }

    public function exportExcel()
    {
        $data = $this->getReportData();

        return Excel::download(
            new CostSummaryExport($data, $this->getDateRangeLabel()),
            'cost-summary-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.client.reports.financial.cost-summary', [
            'data' => $this->getReportData(),
        ]);
    }
}

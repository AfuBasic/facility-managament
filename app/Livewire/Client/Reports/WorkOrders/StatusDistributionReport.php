<?php

namespace App\Livewire\Client\Reports\WorkOrders;

use App\Exports\WorkOrderStatusExport;
use App\Livewire\Concerns\HasDateRangeFilter;
use App\Models\ClientAccount;
use App\Models\WorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.client-app')]
#[Title('Work Order Status Report | Optima FM')]
class StatusDistributionReport extends Component
{
    use HasDateRangeFilter;

    public ClientAccount $clientAccount;

    public function mount(): void
    {
        $this->authorize('view reports');
        $this->clientAccount = app(ClientAccount::class);
    }

    protected function getClientId(): int
    {
        return $this->clientAccount->id ?? app(ClientAccount::class)->id;
    }

    public function getReportData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        // Status Distribution
        $statusDistribution = WorkOrder::where('client_account_id', $this->getClientId())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->status => $item->count])
            ->toArray();

        $statuses = ['reported', 'approved', 'assigned', 'in_progress', 'on_hold', 'completed', 'closed', 'rejected'];
        $statusData = [];
        $total = array_sum($statusDistribution);

        foreach ($statuses as $status) {
            $count = $statusDistribution[$status] ?? 0;
            $statusData[] = [
                'status' => ucfirst(str_replace('_', ' ', $status)),
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
            ];
        }

        // Priority Distribution
        $priorityDistribution = WorkOrder::where('client_account_id', $this->getClientId())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->priority => $item->count])
            ->toArray();

        $priorities = ['low', 'medium', 'high', 'critical'];
        $priorityData = [];

        foreach ($priorities as $priority) {
            $priorityData[] = [
                'priority' => ucfirst($priority),
                'count' => $priorityDistribution[$priority] ?? 0,
            ];
        }

        // Trend Data (daily for last 30 days, monthly for longer)
        $daysDiff = $startDate->diffInDays($endDate);
        $trendData = [];

        if ($daysDiff <= 31) {
            // Daily trend
            $trend = WorkOrder::where('client_account_id', $this->getClientId())
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            foreach ($trend as $item) {
                $trendData[] = [
                    'label' => \Carbon\Carbon::parse($item->date)->format('M d'),
                    'value' => $item->count,
                ];
            }
        } else {
            // Monthly trend
            $trend = WorkOrder::where('client_account_id', $this->getClientId())
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            foreach ($trend as $item) {
                $trendData[] = [
                    'label' => \Carbon\Carbon::parse($item->month.'-01')->format('M Y'),
                    'value' => $item->count,
                ];
            }
        }

        // Summary metrics
        $avgCompletionTime = WorkOrder::where('client_account_id', $this->getClientId())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('completed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as avg_hours')
            ->value('avg_hours');

        return [
            'total' => $total,
            'statusData' => $statusData,
            'priorityData' => $priorityData,
            'trendData' => $trendData,
            'avgCompletionTime' => $avgCompletionTime ? round($avgCompletionTime, 1) : 0,
            'openOrders' => ($statusDistribution['reported'] ?? 0) + ($statusDistribution['approved'] ?? 0) + ($statusDistribution['assigned'] ?? 0) + ($statusDistribution['in_progress'] ?? 0),
            'completedOrders' => ($statusDistribution['completed'] ?? 0) + ($statusDistribution['closed'] ?? 0),
        ];
    }

    public function exportPdf()
    {
        $data = $this->getReportData();

        $pdf = Pdf::loadView('exports.pdf.reports.status-distribution', [
            'data' => $data,
            'title' => 'Work Order Status Distribution Report',
            'dateRange' => $this->getDateRangeLabel(),
            'generatedAt' => now(),
            'clientName' => $this->clientAccount->name,
        ]);

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'work-order-status-report-'.now()->format('Y-m-d').'.pdf'
        );
    }

    public function exportExcel()
    {
        $data = $this->getReportData();

        return Excel::download(
            new WorkOrderStatusExport($data, $this->getDateRangeLabel()),
            'work-order-status-report-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function render()
    {
        $data = $this->getReportData();

        return view('livewire.client.reports.work-orders.status-distribution', [
            'data' => $data,
        ]);
    }
}

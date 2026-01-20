<?php

namespace App\Livewire\Client\Reports\WorkOrders;

use App\Exports\SlaComplianceExport;
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
#[Title('SLA Compliance Report | Optima FM')]
class SlaComplianceReport extends Component
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

        // Get work orders in date range
        $workOrders = WorkOrder::where('client_account_id', $this->getClientId())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $total = $workOrders->count();
        $responseBreached = $workOrders->where('sla_response_breached', true)->count();
        $resolutionBreached = $workOrders->where('sla_resolution_breached', true)->count();

        // Calculate compliance rates
        $responseCompliant = $total > 0 ? $total - $responseBreached : 0;
        $resolutionCompliant = $total > 0 ? $total - $resolutionBreached : 0;

        $responseComplianceRate = $total > 0 ? round(($responseCompliant / $total) * 100, 1) : 100;
        $resolutionComplianceRate = $total > 0 ? round(($resolutionCompliant / $total) * 100, 1) : 100;

        // Overall compliance (not breached either)
        $fullyCompliant = $workOrders->where('sla_response_breached', false)
            ->where('sla_resolution_breached', false)
            ->count();
        $overallComplianceRate = $total > 0 ? round(($fullyCompliant / $total) * 100, 1) : 100;

        // Currently overdue (active work orders past deadline)
        $currentlyOverdue = WorkOrder::where('client_account_id', $this->getClientId())
            ->whereIn('status', ['reported', 'approved', 'assigned', 'in_progress', 'on_hold'])
            ->where(function ($q) {
                $q->where('response_due_at', '<', now())
                    ->orWhere('resolution_due_at', '<', now());
            })
            ->count();

        // Monthly trend for SLA compliance
        $trendData = [];
        $daysDiff = $startDate->diffInDays($endDate);

        if ($daysDiff <= 31) {
            // Weekly trend
            $weeks = ceil($daysDiff / 7);
            $currentStart = $startDate->copy();

            for ($i = 0; $i < $weeks && $i < 5; $i++) {
                $weekEnd = $currentStart->copy()->addDays(6);
                if ($weekEnd > $endDate) {
                    $weekEnd = $endDate->copy();
                }

                $weekOrders = WorkOrder::where('client_account_id', $this->getClientId())
                    ->whereBetween('created_at', [$currentStart, $weekEnd])
                    ->get();

                $weekTotal = $weekOrders->count();
                $weekCompliant = $weekOrders->where('sla_response_breached', false)
                    ->where('sla_resolution_breached', false)
                    ->count();

                $trendData[] = [
                    'label' => 'Week '.($i + 1),
                    'total' => $weekTotal,
                    'compliant' => $weekCompliant,
                    'rate' => $weekTotal > 0 ? round(($weekCompliant / $weekTotal) * 100, 1) : 100,
                ];

                $currentStart = $weekEnd->copy()->addDay();
            }
        } else {
            // Monthly trend
            $trend = WorkOrder::where('client_account_id', $this->getClientId())
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('count(*) as total'),
                    DB::raw('SUM(CASE WHEN sla_response_breached = 0 AND sla_resolution_breached = 0 THEN 1 ELSE 0 END) as compliant')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            foreach ($trend as $item) {
                $trendData[] = [
                    'label' => \Carbon\Carbon::parse($item->month.'-01')->format('M Y'),
                    'total' => $item->total,
                    'compliant' => $item->compliant,
                    'rate' => $item->total > 0 ? round(($item->compliant / $item->total) * 100, 1) : 100,
                ];
            }
        }

        // By Priority breakdown
        $byPriority = WorkOrder::where('client_account_id', $this->getClientId())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'priority',
                DB::raw('count(*) as total'),
                DB::raw('SUM(CASE WHEN sla_response_breached = 1 THEN 1 ELSE 0 END) as response_breached'),
                DB::raw('SUM(CASE WHEN sla_resolution_breached = 1 THEN 1 ELSE 0 END) as resolution_breached')
            )
            ->groupBy('priority')
            ->get()
            ->map(function ($item) {
                $compliant = $item->total - max($item->response_breached, $item->resolution_breached);

                return [
                    'priority' => ucfirst($item->priority),
                    'total' => $item->total,
                    'response_breached' => $item->response_breached,
                    'resolution_breached' => $item->resolution_breached,
                    'compliance_rate' => $item->total > 0 ? round(($compliant / $item->total) * 100, 1) : 100,
                ];
            })
            ->toArray();

        return [
            'total' => $total,
            'responseBreached' => $responseBreached,
            'resolutionBreached' => $resolutionBreached,
            'responseComplianceRate' => $responseComplianceRate,
            'resolutionComplianceRate' => $resolutionComplianceRate,
            'overallComplianceRate' => $overallComplianceRate,
            'currentlyOverdue' => $currentlyOverdue,
            'trendData' => $trendData,
            'byPriority' => $byPriority,
        ];
    }

    public function exportPdf()
    {
        $data = $this->getReportData();

        $pdf = Pdf::loadView('exports.pdf.reports.sla-compliance', [
            'data' => $data,
            'title' => 'SLA Compliance Report',
            'dateRange' => $this->getDateRangeLabel(),
            'generatedAt' => now(),
            'clientName' => $this->clientAccount->name,
        ]);

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'sla-compliance-report-'.now()->format('Y-m-d').'.pdf'
        );
    }

    public function exportExcel()
    {
        $data = $this->getReportData();

        return Excel::download(
            new SlaComplianceExport($data, $this->getDateRangeLabel()),
            'sla-compliance-report-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function render()
    {
        $data = $this->getReportData();

        return view('livewire.client.reports.work-orders.sla-compliance', [
            'data' => $data,
        ]);
    }
}

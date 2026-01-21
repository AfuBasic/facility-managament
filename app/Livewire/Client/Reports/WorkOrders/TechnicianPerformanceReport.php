<?php

namespace App\Livewire\Client\Reports\WorkOrders;

use App\Exports\TechnicianPerformanceExport;
use App\Livewire\Concerns\HasDateRangeFilter;
use App\Models\ClientAccount;
use App\Models\User;
use App\Models\WorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.client-app')]
#[Title('Technician Performance Report | Optima FM')]
class TechnicianPerformanceReport extends Component
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

        // Get technician performance data
        $technicianData = WorkOrder::where('client_account_id', $this->getClientId())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('assigned_to')
            ->select(
                'assigned_to',
                DB::raw('count(*) as total_assigned'),
                DB::raw('SUM(CASE WHEN status = "completed" OR status = "closed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) as in_progress'),
                DB::raw('SUM(CASE WHEN status = "on_hold" THEN 1 ELSE 0 END) as on_hold'),
                DB::raw('SUM(CASE WHEN sla_response_breached = 0 AND sla_resolution_breached = 0 THEN 1 ELSE 0 END) as sla_compliant'),
                DB::raw('AVG(CASE WHEN completed_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR, assigned_at, completed_at) ELSE NULL END) as avg_completion_hours'),
                DB::raw('SUM(COALESCE(total_cost, 0)) as total_cost')
            )
            ->groupBy('assigned_to')
            ->get();

        // Enrich with user names
        $userIds = $technicianData->pluck('assigned_to')->toArray();
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        $technicians = $technicianData->map(function ($item) use ($users) {
            $user = $users->get($item->assigned_to);
            $completionRate = $item->total_assigned > 0
                ? round(($item->completed / $item->total_assigned) * 100, 1)
                : 0;
            $slaRate = $item->total_assigned > 0
                ? round(($item->sla_compliant / $item->total_assigned) * 100, 1)
                : 0;

            return [
                'id' => $item->assigned_to,
                'name' => $user ? $user->name : 'Unknown',
                'total_assigned' => $item->total_assigned,
                'completed' => $item->completed,
                'in_progress' => $item->in_progress,
                'on_hold' => $item->on_hold,
                'completion_rate' => $completionRate,
                'sla_rate' => $slaRate,
                'avg_completion_hours' => $item->avg_completion_hours ? round($item->avg_completion_hours, 1) : null,
                'total_cost' => $item->total_cost ?? 0,
            ];
        })->sortByDesc('total_assigned')->values()->toArray();

        // Summary metrics
        $totalAssigned = array_sum(array_column($technicians, 'total_assigned'));
        $totalCompleted = array_sum(array_column($technicians, 'completed'));
        $avgCompletionRate = count($technicians) > 0
            ? round(array_sum(array_column($technicians, 'completion_rate')) / count($technicians), 1)
            : 0;
        $avgSlaRate = count($technicians) > 0
            ? round(array_sum(array_column($technicians, 'sla_rate')) / count($technicians), 1)
            : 0;

        // Top performers (by completion rate, minimum 5 assignments)
        $topPerformers = collect($technicians)
            ->filter(fn ($t) => $t['total_assigned'] >= 5)
            ->sortByDesc('completion_rate')
            ->take(5)
            ->values()
            ->toArray();

        // Chart data - workload distribution
        $workloadData = collect($technicians)
            ->take(10)
            ->map(fn ($t) => [
                'name' => $t['name'],
                'value' => $t['total_assigned'],
            ])
            ->toArray();

        return [
            'technicians' => $technicians,
            'totalTechnicians' => count($technicians),
            'totalAssigned' => $totalAssigned,
            'totalCompleted' => $totalCompleted,
            'avgCompletionRate' => $avgCompletionRate,
            'avgSlaRate' => $avgSlaRate,
            'topPerformers' => $topPerformers,
            'workloadData' => $workloadData,
        ];
    }

    public function exportPdf()
    {
        $data = $this->getReportData();

        $pdf = Pdf::loadView('exports.pdf.reports.technician-performance', [
            'data' => $data,
            'title' => 'Technician Performance Report',
            'dateRange' => $this->getDateRangeLabel(),
            'generatedAt' => now(),
            'clientName' => $this->clientAccount->name,
        ]);

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'technician-performance-report-'.now()->format('Y-m-d').'.pdf'
        );
    }

    public function exportExcel()
    {
        $data = $this->getReportData();

        return Excel::download(
            new TechnicianPerformanceExport($data, $this->getDateRangeLabel(), $this->clientAccount->getCurrencySymbol()),
            'technician-performance-report-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function render()
    {
        $data = $this->getReportData();

        return view('livewire.client.reports.work-orders.technician-performance', [
            'data' => $data,
        ]);
    }
}

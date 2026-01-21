<?php

namespace App\Exports;

use App\Models\ClientAccount;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkOrderListExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected string $currency = '$';

    public function __construct(
        protected string $status = '',
        protected string $priority = '',
        protected string $search = ''
    ) {
        $clientAccount = app(ClientAccount::class);
        $this->currency = $clientAccount->getCurrencySymbol();
    }

    public function query()
    {
        $clientAccount = app(ClientAccount::class);
        $clientId = $clientAccount->id ?? session('current_client_account_id');
        $user = Auth::user();

        $query = WorkOrder::where('client_account_id', $clientId)
            ->with(['facility', 'reportedBy', 'assignedTo']);

        // If user doesn't have general permission, only show their work orders
        if (! $user->can('view workorders')) {
            $query->where(function ($q) use ($user) {
                $q->where('reported_by', $user->id)
                    ->orWhere('assigned_to', $user->id);
            });
        }

        // Apply filters
        return $query
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->priority, fn ($q) => $q->where('priority', $this->priority))
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('title', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->latest('created_at');
    }

    public function map($workOrder): array
    {
        return [
            $workOrder->serial ?? $workOrder->id,
            $workOrder->title,
            $workOrder->facility?->name ?? 'N/A',
            ucfirst($workOrder->status),
            ucfirst($workOrder->priority),
            $workOrder->reportedBy?->name ?? 'N/A',
            $workOrder->assignedTo?->name ?? 'Unassigned',
            $workOrder->created_at?->format('M d, Y'),
            $workOrder->completed_at?->format('M d, Y') ?? '-',
            $workOrder->total_cost ? $this->currency.number_format($workOrder->total_cost, 2) : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Serial #',
            'Title',
            'Facility',
            'Status',
            'Priority',
            'Reported By',
            'Assigned To',
            'Created',
            'Completed',
            'Total Cost',
        ];
    }

    public function title(): string
    {
        return 'Work Orders';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '14B8A6'],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ];
    }
}

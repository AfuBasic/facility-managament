<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaintenanceHistoryExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected array $data;
    protected string $dateRange;

    public function __construct(array $data, string $dateRange)
    {
        $this->data = $data;
        $this->dateRange = $dateRange;
    }

    public function title(): string
    {
        return 'Maintenance History';
    }

    public function headings(): array
    {
        return [
            'Facility',
            'Total Orders',
            'Completed',
            'Open',
            'Total Cost',
            'Avg Completion (hours)',
        ];
    }

    public function array(): array
    {
        return array_map(fn($facility) => [
            $facility['name'],
            $facility['total_orders'],
            $facility['completed'],
            $facility['open'],
            $facility['total_cost'],
            $facility['avg_completion_hours'],
        ], $this->data['facilityData']);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

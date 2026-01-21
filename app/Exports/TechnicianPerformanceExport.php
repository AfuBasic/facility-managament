<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TechnicianPerformanceExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        protected array $data,
        protected string $dateRange,
        protected string $currency = '$'
    ) {}

    public function array(): array
    {
        $rows = [];

        foreach ($this->data['technicians'] as $tech) {
            $rows[] = [
                $tech['name'],
                $tech['total_assigned'],
                $tech['completed'],
                $tech['in_progress'],
                $tech['on_hold'],
                $tech['completion_rate'].'%',
                $tech['sla_rate'].'%',
                $tech['avg_completion_hours'] ? $tech['avg_completion_hours'].'h' : 'N/A',
                $this->currency.number_format($tech['total_cost'], 2),
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Technician',
            'Assigned',
            'Completed',
            'In Progress',
            'On Hold',
            'Completion Rate',
            'SLA Rate',
            'Avg Completion Time',
            'Total Cost',
        ];
    }

    public function title(): string
    {
        return 'Technician Performance';
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

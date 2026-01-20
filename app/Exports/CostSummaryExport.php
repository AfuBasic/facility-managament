<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CostSummaryExport implements FromArray, WithHeadings, WithStyles, WithTitle
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
        return 'Cost Summary';
    }

    public function headings(): array
    {
        return [
            'Facility',
            'Order Count',
            'Total Cost',
            'Average Cost',
        ];
    }

    public function array(): array
    {
        return array_map(fn($facility) => [
            $facility['facility'],
            $facility['order_count'],
            $facility['total_cost'],
            $facility['avg_cost'],
        ], $this->data['costByFacility']);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

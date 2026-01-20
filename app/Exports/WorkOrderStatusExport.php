<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkOrderStatusExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        protected array $data,
        protected string $dateRange
    ) {}

    public function array(): array
    {
        $rows = [];

        // Status distribution rows
        foreach ($this->data['statusData'] as $item) {
            $rows[] = [
                $item['status'],
                $item['count'],
                $item['percentage'].'%',
            ];
        }

        // Add empty row
        $rows[] = ['', '', ''];

        // Add summary row
        $rows[] = ['Total', $this->data['total'], '100%'];

        return $rows;
    }

    public function headings(): array
    {
        return ['Status', 'Count', 'Percentage'];
    }

    public function title(): string
    {
        return 'Status Distribution';
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

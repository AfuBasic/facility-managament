<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SlaComplianceExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        protected array $data,
        protected string $dateRange
    ) {}

    public function array(): array
    {
        $rows = [];

        // Summary row
        $rows[] = ['Summary', '', '', '', ''];
        $rows[] = ['Total Work Orders', $this->data['total'], '', '', ''];
        $rows[] = ['Overall Compliance Rate', $this->data['overallComplianceRate'].'%', '', '', ''];
        $rows[] = ['Response Compliance Rate', $this->data['responseComplianceRate'].'%', '', '', ''];
        $rows[] = ['Resolution Compliance Rate', $this->data['resolutionComplianceRate'].'%', '', '', ''];
        $rows[] = ['', '', '', '', ''];

        // By Priority breakdown
        $rows[] = ['By Priority', 'Total', 'Response Breached', 'Resolution Breached', 'Compliance Rate'];
        foreach ($this->data['byPriority'] as $item) {
            $rows[] = [
                $item['priority'],
                $item['total'],
                $item['response_breached'],
                $item['resolution_breached'],
                $item['compliance_rate'].'%',
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['Metric', 'Value', 'Col3', 'Col4', 'Col5'];
    }

    public function title(): string
    {
        return 'SLA Compliance';
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

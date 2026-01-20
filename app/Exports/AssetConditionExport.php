<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetConditionExport implements FromArray, WithHeadings, WithStyles, WithTitle
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
        return 'Asset Condition';
    }

    public function headings(): array
    {
        return [
            'Name',
            'Serial',
            'Type',
            'Facility',
            'Status',
            'Assigned To',
            'Purchased At',
        ];
    }

    public function array(): array
    {
        return array_map(fn($asset) => [
            $asset['name'],
            $asset['serial'],
            $asset['type'],
            $asset['facility'],
            $asset['status'],
            $asset['assigned_to'],
            $asset['purchased_at'],
        ], $this->data['assetList']);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

<?php

namespace App\Livewire\Client\Reports\Facilities;

use App\Exports\AssetConditionExport;
use App\Livewire\Concerns\HasDateRangeFilter;
use App\Models\Asset;
use App\Models\ClientAccount;
use App\Models\Facility;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.client-app')]
#[Title('Asset Condition Report | Optima FM')]
class AssetConditionReport extends Component
{
    use HasDateRangeFilter;

    public ClientAccount $clientAccount;
    public ?int $facilityId = null;
    public string $assetType = '';

    public function mount(): void
    {
        $this->authorize('view reports');
        $this->clientAccount = app(ClientAccount::class);
    }

    protected function getClientId(): int
    {
        return $this->clientAccount->id ?? app(ClientAccount::class)->id;
    }

    public function getFacilitiesProperty()
    {
        return Facility::where('client_account_id', $this->getClientId())
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function getAssetTypesProperty()
    {
        return Asset::where('client_account_id', $this->getClientId())
            ->whereNotNull('type')
            ->distinct()
            ->pluck('type', 'type');
    }

    public function getReportData(): array
    {
        $query = Asset::where('client_account_id', $this->getClientId())
            ->when($this->facilityId, fn($q) => $q->where('facility_id', $this->facilityId))
            ->when($this->assetType, fn($q) => $q->where('type', $this->assetType));

        // Assets by Type
        $byType = Asset::where('client_account_id', $this->getClientId())
            ->when($this->facilityId, fn($q) => $q->where('facility_id', $this->facilityId))
            ->when($this->assetType, fn($q) => $q->where('type', $this->assetType))
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get()
            ->map(fn($item) => [
                'type' => $item->type ?: 'Uncategorized',
                'count' => $item->count,
            ])
            ->toArray();

        // Assets by Facility
        $byFacility = Asset::where('assets.client_account_id', $this->getClientId())
            ->when($this->facilityId, fn($q) => $q->where('assets.facility_id', $this->facilityId))
            ->when($this->assetType, fn($q) => $q->where('assets.type', $this->assetType))
            ->leftJoin('facilities', 'assets.facility_id', '=', 'facilities.id')
            ->select(
                'facilities.name as facility_name',
                DB::raw('COUNT(assets.id) as total'),
                DB::raw('SUM(CASE WHEN assets.assigned_to_user_id IS NOT NULL THEN 1 ELSE 0 END) as checked_out')
            )
            ->groupBy('facilities.id', 'facilities.name')
            ->orderBy('total', 'desc')
            ->get()
            ->map(fn($item) => [
                'facility' => $item->facility_name ?: 'Unassigned',
                'total' => $item->total,
                'checked_out' => $item->checked_out,
                'available' => $item->total - $item->checked_out,
            ])
            ->toArray();

        // Asset Details List (top 50)
        $assetList = Asset::where('assets.client_account_id', $this->getClientId())
            ->when($this->facilityId, fn($q) => $q->where('assets.facility_id', $this->facilityId))
            ->when($this->assetType, fn($q) => $q->where('assets.type', $this->assetType))
            ->leftJoin('facilities', 'assets.facility_id', '=', 'facilities.id')
            ->leftJoin('users', 'assets.assigned_to_user_id', '=', 'users.id')
            ->select(
                'assets.id',
                'assets.name',
                'assets.serial',
                'assets.type',
                'assets.units',
                'assets.assigned_to_user_id',
                'facilities.name as facility_name',
                'users.name as assigned_to',
                'assets.purchased_at'
            )
            ->orderBy('assets.name')
            ->limit(50)
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'name' => $a->name,
                'serial' => $a->serial ?: '-',
                'type' => $a->type ?: '-',
                'units' => $a->units ?: 1,
                'facility' => $a->facility_name ?: 'Unassigned',
                'assigned_to' => $a->assigned_to ?: '-',
                'status' => $a->assigned_to_user_id ? 'Checked Out' : 'Available',
                'purchased_at' => $a->purchased_at ? \Carbon\Carbon::parse($a->purchased_at)->format('M d, Y') : '-',
            ])
            ->toArray();

        // Summary
        $totalAssets = $query->clone()->count();
        $checkedOut = $query->clone()->whereNotNull('assigned_to_user_id')->count();

        return [
            'byType' => $byType,
            'byFacility' => $byFacility,
            'assetList' => $assetList,
            'summary' => [
                'total' => $totalAssets,
                'checked_out' => $checkedOut,
                'available' => $totalAssets - $checkedOut,
                'types_count' => count($byType),
                'facilities_count' => count($byFacility),
            ],
        ];
    }

    public function exportPdf()
    {
        $data = $this->getReportData();

        $pdf = Pdf::loadView('exports.pdf.reports.asset-condition', [
            'data' => $data,
            'title' => 'Asset Condition Report',
            'dateRange' => $this->getDateRangeLabel(),
            'generatedAt' => now(),
            'clientName' => $this->clientAccount->name,
        ]);

        return response()->streamDownload(
            fn() => print($pdf->output()),
            'asset-condition-report-' . now()->format('Y-m-d') . '.pdf'
        );
    }

    public function exportExcel()
    {
        $data = $this->getReportData();

        return Excel::download(
            new AssetConditionExport($data, $this->getDateRangeLabel()),
            'asset-condition-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.client.reports.facilities.asset-condition', [
            'data' => $this->getReportData(),
        ]);
    }
}

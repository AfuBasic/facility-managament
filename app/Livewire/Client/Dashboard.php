<?php

namespace App\Livewire\Client;

use App\Models\Asset;
use App\Models\ClientAccount;
use App\Models\Event;
use App\Models\Facility;
use App\Models\WorkOrder;
use App\Models\WorkOrderHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
#[Title('Dashboard | Optima FM')]
class Dashboard extends Component
{
    public $stats = [];

    public $woVolume = [];

    public $woStatus = [];

    public $recentActivity = [];

    public $upcomingEvents = [];

    public $eventDates = [];

    public $slaMetrics = [];

    public $recentWorkOrders = [];

    protected $clientId;

    public function mount()
    {
        $this->clientId = $this->getClientId();

        $this->fetchStats();
        $this->fetchChartData();
        $this->fetchRecentActivity();
        $this->fetchUpcomingEvents();
        $this->fetchSlaMetrics();
        $this->fetchRecentWorkOrders();
    }

    protected function getClientId()
    {
        try {
            if (app()->bound(ClientAccount::class)) {
                $client = app(ClientAccount::class);
                if ($client && $client->id) {
                    return $client->id;
                }
            }
        } catch (\Exception $e) {
        }

        return session('current_client_account_id');
    }

    public function fetchStats()
    {
        $clientId = $this->clientId ?? $this->getClientId();

        $this->stats = [
            'open_orders' => WorkOrder::where('client_account_id', $clientId)->whereIn('status', ['open', 'in_progress'])->count(),
            'pending_approval' => WorkOrder::where('client_account_id', $clientId)->where('status', 'pending')->count(),
            'active_facilities' => Facility::where('client_account_id', $clientId)->count(),
            'total_assets' => Asset::where('client_account_id', $clientId)->count(),
        ];
    }

    public function fetchChartData()
    {
        $clientId = $this->clientId ?? $this->getClientId();

        // Work Order Volume (Last 6 months)
        $volume = WorkOrder::where('client_account_id', $clientId)
            ->select(
                DB::raw('count(id) as count'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_year"),
                DB::raw('MAX(created_at) as created_at')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month_year')
            ->orderBy('month_year')
            ->get();

        $this->woVolume = [
            'labels' => $volume->map(fn ($item) => $item->created_at->format('M Y'))->toArray(),
            'data' => $volume->pluck('count')->toArray(),
        ];

        // Status Distribution
        $statuses = ['open', 'in_progress', 'on_hold', 'completed', 'cancelled'];
        $distribution = WorkOrder::where('client_account_id', $clientId)
            ->select('status', DB::raw('count(*) as count'))
            ->whereIn('status', $statuses)
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $this->woStatus = [
            'labels' => array_map(fn ($s) => ucfirst(str_replace('_', ' ', $s)), $statuses),
            'data' => array_map(fn ($s) => $distribution->get($s, 0), $statuses),
        ];
    }

    public function fetchRecentActivity()
    {
        $clientId = $this->clientId ?? $this->getClientId();

        $this->recentActivity = WorkOrderHistory::whereHas('workOrder', fn ($q) => $q->where('client_account_id', $clientId))
            ->with(['changedBy', 'workOrder'])
            ->latest()
            ->take(8)
            ->get();
    }

    public function fetchUpcomingEvents()
    {
        $clientId = $this->clientId ?? $this->getClientId();

        $this->upcomingEvents = Event::where('client_account_id', $clientId)
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->take(5)
            ->get();

        $this->eventDates = Event::where('client_account_id', $clientId)
            ->whereBetween('starts_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->select(DB::raw('DATE(starts_at) as date'))
            ->distinct()
            ->pluck('date')
            ->toArray();
    }

    public function fetchSlaMetrics()
    {
        $clientId = $this->clientId ?? $this->getClientId();

        // Get work orders from the last 30 days for SLA calculations
        $recentOrders = WorkOrder::where('client_account_id', $clientId)
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        $total = $recentOrders->count();
        $responseBreached = $recentOrders->where('sla_response_breached', true)->count();
        $resolutionBreached = $recentOrders->where('sla_resolution_breached', true)->count();

        // Calculate compliance rate
        $compliant = $total > 0 ? $total - ($responseBreached + $resolutionBreached) : 0;
        $complianceRate = $total > 0 ? round(($compliant / $total) * 100) : 100;

        // Get overdue work orders (response or resolution)
        $overdueCount = WorkOrder::where('client_account_id', $clientId)
            ->whereIn('status', ['reported', 'approved', 'assigned', 'in_progress', 'on_hold'])
            ->where(function ($q) {
                $q->where('response_due_at', '<', now())
                    ->orWhere('resolution_due_at', '<', now());
            })
            ->count();

        $this->slaMetrics = [
            'compliance_rate' => $complianceRate,
            'response_breached' => $responseBreached,
            'resolution_breached' => $resolutionBreached,
            'overdue_count' => $overdueCount,
            'total_this_month' => $total,
        ];
    }

    public function fetchRecentWorkOrders()
    {
        $clientId = $this->clientId ?? $this->getClientId();

        $this->recentWorkOrders = WorkOrder::where('client_account_id', $clientId)
            ->with(['facility', 'assignedTo'])
            ->latest()
            ->take(5)
            ->get();
    }

    public function toJSON()
    {
        return [];
    }

    public function render()
    {
        return view('livewire.client.dashboard.index');
    }
}

<div class="space-y-6" x-data="dashboardCharts()">

    <!-- Personalized Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div>
            @php
                $hour = now()->hour;
                $greeting = match(true) {
                    $hour >= 5 && $hour < 12 => 'Good morning',
                    $hour >= 12 && $hour < 17 => 'Good afternoon',
                    $hour >= 17 && $hour < 21 => 'Good evening',
                    default => 'Good night'
                };
            @endphp
            <p class="text-sm font-medium text-slate-500 mb-1">{{ now()->format('l, F j, Y') }}</p>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">
                {{ $greeting }}, {{ auth()->user()->firstname ?? explode(' ', auth()->user()->name)[0] }}
            </h1>
            <p class="text-slate-500 mt-2">Here's what's happening with your facilities today.</p>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('app.work-orders.index', ['create' => 'true']) }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-700 hover:border-teal-300 hover:text-teal-700 hover:shadow-sm transition-all">
                <x-heroicon-o-plus class="h-4 w-4" />
                New Work Order
            </a>
            <a href="{{ route('app.events.index', ['create' => 'true']) }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-700 hover:border-teal-300 hover:text-teal-700 hover:shadow-sm transition-all">
                <x-heroicon-o-calendar class="h-4 w-4" />
                Schedule Event
            </a>
            <a href="{{ route('app.facilities') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-teal-600 to-teal-500 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 shadow-sm hover:shadow-md transition-all">
                <x-heroicon-o-building-office-2 class="h-4 w-4" />
                View Facilities
            </a>
        </div>
    </div>

    <!-- Attention Banner -->
    @if(($stats['pending_approval'] ?? 0) > 0 || ($slaMetrics['overdue_count'] ?? 0) > 0)
    <div class="p-4 rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100 flex items-start gap-3">
        <div class="p-2 rounded-lg bg-amber-100">
            <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-amber-600" />
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-semibold text-amber-900">Attention needed</h3>
            <p class="text-sm text-amber-700 mt-0.5">
                @if(($stats['pending_approval'] ?? 0) > 0)
                    You have <a href="{{ route('app.work-orders.index', ['status' => 'pending']) }}" wire:navigate class="font-semibold underline">{{ $stats['pending_approval'] }} work order{{ $stats['pending_approval'] > 1 ? 's' : '' }}</a> awaiting approval.
                @endif
                @if(($slaMetrics['overdue_count'] ?? 0) > 0)
                    {{ ($stats['pending_approval'] ?? 0) > 0 ? ' Also, ' : '' }}<a href="{{ route('app.work-orders.index') }}" wire:navigate class="font-semibold underline">{{ $slaMetrics['overdue_count'] }} work order{{ $slaMetrics['overdue_count'] > 1 ? 's are' : ' is' }}</a> past SLA deadline.
                @endif
            </p>
        </div>
        <a href="{{ route('app.work-orders.index') }}" wire:navigate class="text-sm font-medium text-amber-700 hover:text-amber-800 whitespace-nowrap">
            View all &rarr;
        </a>
    </div>
    @endif

    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard.stat-card
            label="Open Orders"
            value="{{ $stats['open_orders'] ?? 0 }}"
            icon="clipboard-document-list"
            color="blue"
            :link="route('app.work-orders.index', ['status' => 'in_progress'])"
        />
        <x-dashboard.stat-card
            label="Pending"
            value="{{ $stats['pending_approval'] ?? 0 }}"
            icon="clock"
            color="amber"
            :link="route('app.work-orders.index', ['status' => 'pending'])"
        />
        <x-dashboard.stat-card
            label="Facilities"
            value="{{ $stats['active_facilities'] ?? 0 }}"
            icon="building-office-2"
            color="teal"
            :link="route('app.facilities')"
        />
        <x-dashboard.stat-card
            label="Assets"
            value="{{ $stats['total_assets'] ?? 0 }}"
            icon="cube"
            color="indigo"
            link="#"
        />
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left: Charts Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Charts Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Donut Chart -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h3 class="font-semibold text-slate-900 mb-4">Work Order Status</h3>
                    <div class="flex flex-col justify-center items-center">
                        <div id="status-chart" class="w-full flex justify-center"></div>
                        <div class="flex flex-wrap justify-center gap-x-4 gap-y-1 mt-3 text-xs text-slate-600">
                            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Open</div>
                            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> In Progress</div>
                            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> On Hold</div>
                            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Completed</div>
                        </div>
                    </div>
                </div>

                <!-- SLA Performance -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-slate-900">SLA Performance</h3>
                        <span class="text-xs text-slate-400">Last 30 days</span>
                    </div>

                    <!-- Compliance Gauge -->
                    <div class="flex items-center justify-center mb-4">
                        <div class="relative w-28 h-28">
                            <svg class="w-28 h-28 transform -rotate-90" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="40" stroke="#e2e8f0" stroke-width="10" fill="none" />
                                <circle
                                    cx="50" cy="50" r="40"
                                    stroke="{{ ($slaMetrics['compliance_rate'] ?? 100) >= 90 ? '#10b981' : (($slaMetrics['compliance_rate'] ?? 100) >= 70 ? '#f59e0b' : '#ef4444') }}"
                                    stroke-width="10"
                                    fill="none"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{ (($slaMetrics['compliance_rate'] ?? 100) / 100) * 251.2 }} 251.2"
                                />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-2xl font-bold text-slate-900">{{ $slaMetrics['compliance_rate'] ?? 100 }}%</span>
                                <span class="text-[10px] text-slate-500 uppercase tracking-wide">Compliant</span>
                            </div>
                        </div>
                    </div>

                    <!-- SLA Breakdown -->
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div class="p-2.5 rounded-xl bg-slate-50">
                            <p class="text-lg font-bold text-slate-900">{{ $slaMetrics['response_breached'] ?? 0 }}</p>
                            <p class="text-[10px] text-slate-500 uppercase">Response Breached</p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50">
                            <p class="text-lg font-bold text-slate-900">{{ $slaMetrics['resolution_breached'] ?? 0 }}</p>
                            <p class="text-[10px] text-slate-500 uppercase">Resolution Breached</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Volume Chart -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-900">Work Order Trend</h3>
                    <span class="text-xs px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">Last 6 months</span>
                </div>
                <div id="volume-chart" class="w-full h-[220px]"></div>
            </div>

            <!-- Recent Work Orders -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-900">Recent Work Orders</h3>
                    <a href="{{ route('app.work-orders.index') }}" wire:navigate class="text-xs font-medium text-teal-600 hover:text-teal-700">View all</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($recentWorkOrders as $workOrder)
                        <a href="{{ route('app.work-orders.index') }}" wire:navigate class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 transition-colors">
                            <!-- Status indicator -->
                            @php
                                $statusColors = [
                                    'reported' => 'bg-slate-400',
                                    'approved' => 'bg-blue-500',
                                    'assigned' => 'bg-indigo-500',
                                    'in_progress' => 'bg-amber-500',
                                    'on_hold' => 'bg-red-500',
                                    'completed' => 'bg-emerald-500',
                                    'closed' => 'bg-slate-400',
                                ];
                                $statusBadgeColors = [
                                    'reported' => 'bg-slate-100 text-slate-700',
                                    'approved' => 'bg-blue-50 text-blue-700',
                                    'assigned' => 'bg-indigo-50 text-indigo-700',
                                    'in_progress' => 'bg-amber-50 text-amber-700',
                                    'on_hold' => 'bg-red-50 text-red-700',
                                    'completed' => 'bg-emerald-50 text-emerald-700',
                                    'closed' => 'bg-slate-100 text-slate-600',
                                ];
                            @endphp
                            <div class="w-1 h-10 rounded-full {{ $statusColors[$workOrder->status] ?? 'bg-slate-300' }}"></div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3">
                                    <p class="text-sm font-medium text-slate-900 truncate">{{ $workOrder->title }}</p>
                                    <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusBadgeColors[$workOrder->status] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst(str_replace('_', ' ', $workOrder->status)) }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xs text-slate-500">{{ $workOrder->workorder_serial }}</span>
                                    @if($workOrder->facility)
                                        <span class="text-xs text-slate-400 flex items-center gap-1">
                                            <x-heroicon-o-building-office-2 class="h-3 w-3" />
                                            {{ $workOrder->facility->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <span class="text-xs text-slate-400">{{ $workOrder->created_at->diffForHumans() }}</span>
                        </a>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <x-heroicon-o-clipboard-document-list class="h-10 w-10 mx-auto text-slate-300" />
                            <p class="text-sm text-slate-500 mt-3">No work orders yet</p>
                            <a href="{{ route('app.work-orders.index', ['create' => 'true']) }}" wire:navigate class="inline-flex items-center gap-1 text-sm font-medium text-teal-600 hover:text-teal-700 mt-2">
                                <x-heroicon-o-plus class="h-4 w-4" />
                                Create your first work order
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Calendar & Activity Column -->
        <div class="space-y-6">
            <!-- Calendar Widget -->
            <x-dashboard.calendar-widget :events="$upcomingEvents" :eventDates="$eventDates" />

            <!-- Activity Feed -->
            <x-dashboard.activity-feed :activities="$recentActivity" />
        </div>

    </div>
</div>

<script>
    function dashboardCharts() {
        return {
            init() {
                // Volume Chart
                const volumeOptions = {
                    series: [{
                        name: 'Work Orders',
                        data: @json($woVolume['data'] ?? [])
                    }],
                    chart: {
                        type: 'area',
                        height: 220,
                        fontFamily: 'Inter, sans-serif',
                        toolbar: { show: false },
                        zoom: { enabled: false },
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        curve: 'smooth',
                        width: 2.5
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.05,
                            stops: [0, 90, 100]
                        }
                    },
                    colors: ['#14b8a6'],
                    xaxis: {
                        categories: @json($woVolume['labels'] ?? []),
                        labels: {
                            style: { colors: '#94a3b8', fontSize: '11px' }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: '#94a3b8', fontSize: '11px' }
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 4,
                        padding: { left: 10, right: 10 }
                    },
                    tooltip: {
                        theme: 'light',
                        y: {
                            formatter: (val) => val + ' orders'
                        }
                    }
                };

                if (document.querySelector('#volume-chart')) {
                    new ApexCharts(document.querySelector('#volume-chart'), volumeOptions).render();
                }

                // Status Donut Chart
                const statusOptions = {
                    series: @json($woStatus['data'] ?? []),
                    labels: @json($woStatus['labels'] ?? []),
                    chart: {
                        type: 'donut',
                        height: 180,
                        fontFamily: 'Inter, sans-serif',
                    },
                    colors: ['#3b82f6', '#f59e0b', '#ef4444', '#10b981', '#64748b'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    name: { show: true, fontSize: '12px', fontFamily: 'Inter, sans-serif', color: '#64748b' },
                                    value: { show: true, fontSize: '20px', fontFamily: 'Inter, sans-serif', fontWeight: 700, color: '#0f172a' },
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        color: '#64748b',
                                        fontSize: '12px',
                                        formatter: function (w) {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                        }
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: { show: false },
                    legend: { show: false }
                };

                if (document.querySelector('#status-chart')) {
                    new ApexCharts(document.querySelector('#status-chart'), statusOptions).render();
                }
            }
        }
    }
</script>

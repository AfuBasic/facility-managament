<div class="h-full" x-data="dashboardCharts()">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
            <p class="text-slate-500 mt-1">Welcome back, {{ auth()->user()->name }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
             <span class="text-sm font-medium text-slate-500 bg-white px-3 py-1.5 rounded-lg border border-slate-200">
                {{ now()->format('l, F j, Y') }}
             </span>
        </div>
    </div>

    <!-- Bento Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-[calc(100vh-200px)]">
        
        <!-- Left Column: Calendar (Tall) -->
        <div class="lg:col-span-3 h-full">
            <x-dashboard.calendar-widget :events="$upcomingEvents" :eventDates="$eventDates" />
        </div>

        <!-- Middle Column: Analytics (Tall split) -->
        <div class="lg:col-span-6 flex flex-col gap-6">
            
            <!-- Top: Split Stats & Donut -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Donut Chart -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col justify-between">
                    <h3 class="font-semibold text-slate-900">Work Order Status</h3>
                    <div class="flex-1 flex flex-col justify-center items-center">
                        <div id="status-chart" class="w-full flex justify-center"></div>
                        <!-- Custom Legend -->
                        <div class="flex justify-center gap-3 mt-2 text-xs">
                            <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Open</div>
                            <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span> In Prog</div>
                            <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Hold</div>
                        </div>
                    </div>
                </div>

                <!-- Stat Cards Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <x-dashboard.stat-card 
                        label="Total Assets" 
                        value="{{ $stats['total_assets'] }}" 
                        icon="cube" 
                        color="indigo"
                    />
                    <x-dashboard.stat-card 
                        label="Active Sites" 
                        value="{{ $stats['active_facilities'] }}" 
                        icon="building-office" 
                        color="emerald"
                    />
                    <x-dashboard.stat-card 
                        label="Open Orders" 
                        value="{{ $stats['open_orders'] }}" 
                        trend="12"
                    />
                    <x-dashboard.stat-card 
                        label="Pending" 
                        value="{{ $stats['pending_approval'] }}" 
                        color="amber"
                    />
                </div>
            </div>

            <!-- Bottom: Volume Chart -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex-1 min-h-[300px]">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-900">Work Order Volume</h3>
                    <select class="text-xs border-none bg-slate-50 rounded-lg text-slate-500 focus:ring-0 cursor-pointer">
                        <option>Last 6 Months</option>
                    </select>
                </div>
                <div id="volume-chart" class="w-full h-full"></div>
            </div>
            
        </div>

        <!-- Right Column: Activity (Tall) -->
        <div class="lg:col-span-3 h-full">
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
                        data: @json($woVolume['data'])
                    }],
                    chart: {
                        type: 'area',
                        height: 280,
                        fontFamily: 'Inter, sans-serif',
                        toolbar: { show: false },
                        zoom: { enabled: false }
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        curve: 'smooth',
                        width: 2
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
                    colors: ['#4f46e5'], // Indigo-600
                    xaxis: {
                        categories: @json($woVolume['labels']),
                        labels: {
                            style: { colors: '#64748b', fontSize: '12px' }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: '#64748b', fontSize: '12px' }
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 4,
                    }
                };
                
                if (document.querySelector('#volume-chart')) {
                    const volumeChart = new ApexCharts(document.querySelector('#volume-chart'), volumeOptions);
                    volumeChart.render();
                }

                // Status Donut Chart
                const statusOptions = {
                    series: @json($woStatus['data']),
                    labels: @json($woStatus['labels']),
                    chart: {
                        type: 'donut',
                        height: 200,
                        fontFamily: 'Inter, sans-serif',
                    },
                    colors: ['#3b82f6', '#f59e0b', '#ef4444', '#10b981', '#64748b'], // Blue, Amber, Red, Emerald, Slate
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '75%',
                                labels: {
                                    show: true,
                                    name: { show: true, fontSize: '14px', fontFamily: 'Inter, sans-serif', color: '#64748b' },
                                    value: { show: true, fontSize: '24px', fontFamily: 'Inter, sans-serif', fontWeight: 700, color: '#0f172a' },
                                    total: { 
                                        show: true, 
                                        label: 'Total', 
                                        color: '#64748b',
                                        fontSize: '14px',
                                        formatter: function (w) {
                                            return w.globals.seriesTotals.reduce((a, b) => {
                                                return a + b
                                            }, 0)
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
                    const statusChart = new ApexCharts(document.querySelector('#status-chart'), statusOptions);
                    statusChart.render();
                }
            }
        }
    }
</script>

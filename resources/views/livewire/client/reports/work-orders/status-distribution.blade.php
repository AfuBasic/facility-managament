<div class="space-y-6" x-data="statusReportCharts()">
    <!-- Back Navigation -->
    <a href="{{ route('app.reports.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-teal-600 transition-colors">
        <x-heroicon-o-arrow-left class="h-4 w-4" />
        Back to Reports
    </a>

    <!-- Page Header -->
    <x-ui.page-header title="Work Order Status Distribution" description="Analyze work orders by status and priority over time.">
        <x-slot:actions>
            <x-reports.export-buttons />
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Filters -->
    <x-ui.card>
        <x-reports.date-range-filter
            :presets="$this->getDateRangePresets()"
            :dateRange="$dateRange"
            :startDate="$startDate"
            :endDate="$endDate"
        />
    </x-ui.card>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard.stat-card
            label="Total Work Orders"
            :value="$data['total']"
            icon="clipboard-document-list"
            color="blue"
        />
        <x-dashboard.stat-card
            label="Open Orders"
            :value="$data['openOrders']"
            icon="clock"
            color="amber"
        />
        <x-dashboard.stat-card
            label="Completed"
            :value="$data['completedOrders']"
            icon="check-circle"
            color="emerald"
        />
        <x-dashboard.stat-card
            label="Avg Completion"
            :value="$data['avgCompletionTime'] . 'h'"
            icon="arrow-trending-up"
            color="teal"
        />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Distribution Donut -->
        <x-ui.card>
            <h3 class="font-semibold text-slate-900 mb-4">Status Distribution</h3>
            <div id="status-donut-chart" class="h-72"></div>
        </x-ui.card>

        <!-- Priority Distribution Bar -->
        <x-ui.card>
            <h3 class="font-semibold text-slate-900 mb-4">Priority Distribution</h3>
            <div id="priority-bar-chart" class="h-72"></div>
        </x-ui.card>
    </div>

    <!-- Trend Chart -->
    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-slate-900">Work Order Trend</h3>
            <span class="text-xs text-slate-500">{{ $this->getDateRangeLabel() }}</span>
        </div>
        <div id="trend-area-chart" class="h-64"></div>
    </x-ui.card>

    <!-- Data Table -->
    <x-ui.card>
        <h3 class="font-semibold text-slate-900 mb-4">Status Breakdown</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Count</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Percentage</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Distribution</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($data['statusData'] as $item)
                        @if($item['count'] > 0)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-2">
                                    @php
                                        $statusColors = [
                                            'Reported' => 'bg-slate-400',
                                            'Approved' => 'bg-blue-500',
                                            'Assigned' => 'bg-indigo-500',
                                            'In progress' => 'bg-amber-500',
                                            'On hold' => 'bg-red-500',
                                            'Completed' => 'bg-emerald-500',
                                            'Closed' => 'bg-slate-600',
                                            'Rejected' => 'bg-rose-500',
                                        ];
                                    @endphp
                                    <span class="h-2.5 w-2.5 rounded-full {{ $statusColors[$item['status']] ?? 'bg-slate-400' }}"></span>
                                    <span class="text-sm font-medium text-slate-900">{{ $item['status'] }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-900 font-semibold">
                                {{ number_format($item['count']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-600">
                                {{ $item['percentage'] }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $statusColors[$item['status']] ?? 'bg-slate-400' }}" style="width: {{ $item['percentage'] }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50">
                    <tr>
                        <td class="px-6 py-3 text-sm font-semibold text-slate-900">Total</td>
                        <td class="px-6 py-3 text-right text-sm font-bold text-slate-900">{{ number_format($data['total']) }}</td>
                        <td class="px-6 py-3 text-right text-sm text-slate-600">100%</td>
                        <td class="px-6 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-ui.card>
</div>

<script>
function statusReportCharts() {
    return {
        init() {
            this.renderStatusDonut();
            this.renderPriorityBar();
            this.renderTrendArea();
        },

        renderStatusDonut() {
            const statusData = @json($data['statusData']);
            const labels = statusData.filter(s => s.count > 0).map(s => s.status);
            const values = statusData.filter(s => s.count > 0).map(s => s.count);

            if (values.length === 0) return;

            const options = {
                series: values,
                labels: labels,
                chart: {
                    type: 'donut',
                    height: 288,
                    fontFamily: 'Inter, sans-serif',
                },
                colors: ['#94a3b8', '#3b82f6', '#6366f1', '#f59e0b', '#ef4444', '#10b981', '#475569', '#f43f5e'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: { show: true, fontSize: '12px', color: '#64748b' },
                                value: { show: true, fontSize: '20px', fontWeight: 700, color: '#0f172a' },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    color: '#64748b',
                                    fontSize: '12px',
                                    formatter: (w) => w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            }
                        }
                    }
                },
                dataLabels: { enabled: false },
                legend: { position: 'bottom', fontSize: '12px' },
                stroke: { show: false }
            };

            if (document.querySelector('#status-donut-chart')) {
                new ApexCharts(document.querySelector('#status-donut-chart'), options).render();
            }
        },

        renderPriorityBar() {
            const priorityData = @json($data['priorityData']);

            const options = {
                series: [{
                    name: 'Work Orders',
                    data: priorityData.map(p => p.count)
                }],
                chart: {
                    type: 'bar',
                    height: 288,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false }
                },
                colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                plotOptions: {
                    bar: {
                        borderRadius: 8,
                        columnWidth: '50%',
                        distributed: true
                    }
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: priorityData.map(p => p.priority),
                    labels: { style: { colors: '#64748b', fontSize: '12px' } }
                },
                yaxis: {
                    labels: { style: { colors: '#64748b', fontSize: '12px' } }
                },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                legend: { show: false }
            };

            if (document.querySelector('#priority-bar-chart')) {
                new ApexCharts(document.querySelector('#priority-bar-chart'), options).render();
            }
        },

        renderTrendArea() {
            const trendData = @json($data['trendData']);

            if (trendData.length === 0) return;

            const options = {
                series: [{
                    name: 'Work Orders',
                    data: trendData.map(t => t.value)
                }],
                chart: {
                    type: 'area',
                    height: 256,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                colors: ['#14b8a6'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2.5 },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    categories: trendData.map(t => t.label),
                    labels: { style: { colors: '#94a3b8', fontSize: '11px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: { style: { colors: '#94a3b8', fontSize: '11px' } }
                },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4, padding: { left: 10, right: 10 } },
                tooltip: {
                    theme: 'light',
                    y: { formatter: (val) => val + ' orders' }
                }
            };

            if (document.querySelector('#trend-area-chart')) {
                new ApexCharts(document.querySelector('#trend-area-chart'), options).render();
            }
        }
    }
}
</script>

<div class="space-y-6" x-data="costReportCharts()">
    <!-- Back Navigation -->
    <a href="{{ route('app.reports.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-teal-600 transition-colors">
        <x-heroicon-o-arrow-left class="h-4 w-4" />
        Back to Reports
    </a>

    <!-- Page Header -->
    <x-ui.page-header title="Cost Summary Report" description="Overall cost breakdown and trends.">
        <x-slot:actions>
            <x-reports.export-buttons />
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Filters -->
    <x-ui.card>
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1">
                <x-reports.date-range-filter
                    :presets="$this->getDateRangePresets()"
                    :dateRange="$dateRange"
                    :startDate="$startDate"
                    :endDate="$endDate"
                />
            </div>
            <div class="w-56">
                <x-forms.searchable-select
                    wire:model.live="facilityId"
                    :options="$this->facilities->toArray()"
                    :selected="$facilityId"
                    placeholder="All Facilities"
                    label="Facility"
                />
            </div>
        </div>
    </x-ui.card>


    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard.stat-card
            label="Total Cost"
            :value="'₦' . $data['summary']['total_cost']"
            icon="banknotes"
            color="emerald"
        />
        <x-dashboard.stat-card
            label="Work Orders"
            :value="$data['summary']['total_orders']"
            icon="clipboard-document-list"
            color="blue"
        />
        <x-dashboard.stat-card
            label="Avg Cost/Order"
            :value="'₦' . $data['summary']['avg_cost']"
            icon="calculator"
            color="teal"
        />
        <x-dashboard.stat-card
            label="Highest Cost"
            :value="'₦' . $data['summary']['max_cost']"
            icon="arrow-trending-up"
            color="amber"
        />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Cost Trend -->
        <x-ui.card>
            <h3 class="font-semibold text-slate-900 mb-4">Monthly Cost Trend</h3>
            <div id="trend-chart" class="h-72"></div>
        </x-ui.card>

        <!-- Cost by Priority -->
        <x-ui.card>
            <h3 class="font-semibold text-slate-900 mb-4">Cost by Priority</h3>
            <div id="priority-chart" class="h-72"></div>
        </x-ui.card>
    </div>

    <!-- Cost by Facility Table -->
    <x-ui.card>
        <h3 class="font-semibold text-slate-900 mb-4">Cost by Facility</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Facility</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Orders</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Total Cost</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Avg Cost</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">% of Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @php
                        $grandTotal = array_sum(array_map(fn($f) => (float) str_replace(',', '', $f['total_cost']), $data['costByFacility']));
                    @endphp
                    @forelse($data['costByFacility'] as $facility)
                        @php
                            $facilityTotal = (float) str_replace(',', '', $facility['total_cost']);
                            $percentage = $grandTotal > 0 ? round(($facilityTotal / $grandTotal) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-slate-900">{{ $facility['facility'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-600">
                                {{ number_format($facility['order_count']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-slate-900">
                                ₦{{ $facility['total_cost'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-600">
                                ₦{{ $facility['avg_cost'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-slate-100 rounded-full h-2 max-w-[100px]">
                                        <div class="h-2 rounded-full bg-teal-500" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-xs text-slate-600">{{ $percentage }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                No cost data available.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <!-- Top Costly Work Orders -->
    <x-ui.card>
        <h3 class="font-semibold text-slate-900 mb-4">Top 10 Costly Work Orders</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Serial</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Facility</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Priority</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Cost</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Created</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($data['topCostlyOrders'] as $order)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('app.work-orders.show', $order['id']) }}" wire:navigate class="text-sm font-medium text-teal-600 hover:text-teal-700">
                                    {{ $order['serial'] }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                {{ Str::limit($order['title'], 30) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $order['facility'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                    {{ $order['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $priorityColors = [
                                        'Low' => 'bg-slate-100 text-slate-600',
                                        'Medium' => 'bg-blue-100 text-blue-700',
                                        'High' => 'bg-amber-100 text-amber-700',
                                        'Critical' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$order['priority']] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $order['priority'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-slate-900">
                                ₦{{ $order['cost'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $order['created_at'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                No work orders with costs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</div>

<script>
function costReportCharts() {
    return {
        init() {
            this.renderTrendChart();
            this.renderPriorityChart();
        },

        renderTrendChart() {
            const trendData = @json($data['monthlyTrend']);
            if (trendData.length === 0) return;

            const options = {
                series: [{
                    name: 'Cost (₦)',
                    type: 'area',
                    data: trendData.map(t => t.cost)
                }, {
                    name: 'Orders',
                    type: 'line',
                    data: trendData.map(t => t.count)
                }],
                chart: {
                    height: 288,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false }
                },
                colors: ['#10b981', '#3b82f6'],
                stroke: { width: [0, 3], curve: 'smooth' },
                fill: {
                    type: ['gradient', 'solid'],
                    gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] }
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: trendData.map(t => t.label),
                    labels: { style: { colors: '#64748b', fontSize: '11px' } }
                },
                yaxis: [
                    { title: { text: 'Cost (₦)' }, labels: { style: { colors: '#64748b' }, formatter: (val) => '₦' + val.toLocaleString() } },
                    { opposite: true, title: { text: 'Orders' }, labels: { style: { colors: '#64748b' } } }
                ],
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                legend: { position: 'top' }
            };

            if (document.querySelector('#trend-chart')) {
                new ApexCharts(document.querySelector('#trend-chart'), options).render();
            }
        },

        renderPriorityChart() {
            const priorityData = @json($data['costByPriority']);
            if (priorityData.length === 0) return;

            const options = {
                series: [{
                    name: 'Cost',
                    data: priorityData.map(p => parseFloat(p.total_cost.replace(/,/g, '')))
                }],
                chart: {
                    type: 'bar',
                    height: 288,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false }
                },
                colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                plotOptions: {
                    bar: { borderRadius: 8, columnWidth: '50%', distributed: true }
                },
                dataLabels: {
                    enabled: true,
                    formatter: (val) => '₦' + val.toLocaleString(),
                    style: { fontSize: '11px' },
                    offsetY: -20
                },
                xaxis: {
                    categories: priorityData.map(p => p.priority),
                    labels: { style: { colors: '#64748b', fontSize: '12px' } }
                },
                yaxis: {
                    labels: { style: { colors: '#64748b' }, formatter: (val) => '₦' + val.toLocaleString() }
                },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                legend: { show: false }
            };

            if (document.querySelector('#priority-chart')) {
                new ApexCharts(document.querySelector('#priority-chart'), options).render();
            }
        }
    }
}
</script>

<div class="space-y-6" x-data="maintenanceReportCharts('{{ $clientAccount->getCurrencySymbol() }}')">
    <!-- Back Navigation -->
    <a href="{{ route('app.reports.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-teal-600 transition-colors">
        <x-heroicon-o-arrow-left class="h-4 w-4" />
        Back to Reports
    </a>

    <!-- Page Header -->
    <x-ui.page-header title="Maintenance History Report" description="Work order history and costs per facility.">
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
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <x-dashboard.stat-card
            label="Total Work Orders"
            :value="$data['summary']['total_orders']"
            icon="clipboard-document-list"
            color="blue"
        />
        <x-dashboard.stat-card
            label="Completed"
            :value="$data['summary']['completed']"
            icon="check-circle"
            color="emerald"
        />
        <x-dashboard.stat-card
            label="Total Cost"
            :value="$clientAccount->getCurrencySymbol() . $data['summary']['total_cost']"
            icon="banknotes"
            color="teal"
        />
        <x-dashboard.stat-card
            label="Avg Completion"
            :value="$data['summary']['avg_hours'] . 'h'"
            icon="clock"
            color="amber"
        />
        <x-dashboard.stat-card
            label="Facilities"
            :value="$data['summary']['facilities_count']"
            icon="building-office-2"
            color="indigo"
        />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Trend -->
        <x-ui.card>
            <h3 class="font-semibold text-slate-900 mb-4">Monthly Maintenance Trend</h3>
            <div id="trend-chart" class="h-72"></div>
        </x-ui.card>

        <!-- Cost Distribution -->
        <x-ui.card>
            <h3 class="font-semibold text-slate-900 mb-4">Cost by Facility</h3>
            <div id="cost-chart" class="h-72"></div>
        </x-ui.card>
    </div>

    <!-- Facility Breakdown Table -->
    <x-ui.card>
        <h3 class="font-semibold text-slate-900 mb-4">Facility Breakdown</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Facility</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Total Orders</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Completed</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Open</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Total Cost</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Avg Completion</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($data['facilityData'] as $facility)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-slate-900">{{ $facility['name'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-slate-900">
                                {{ number_format($facility['total_orders']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-emerald-600">
                                {{ number_format($facility['completed']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-amber-600">
                                {{ number_format($facility['open']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-900">
                                {{ $clientAccount->getCurrencySymbol() }}{{ $facility['total_cost'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-600">
                                {{ $facility['avg_completion_hours'] }}h
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                No data available for the selected period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</div>

<script>
function maintenanceReportCharts(currencySymbol) {
    return {
        currency: currencySymbol,
        init() {
            this.renderTrendChart();
            this.renderCostChart();
        },

        renderTrendChart() {
            const trendData = @json($data['trendData']);
            if (trendData.length === 0) return;
            const currency = this.currency;

            const options = {
                series: [{
                    name: 'Work Orders',
                    type: 'column',
                    data: trendData.map(t => t.count)
                }, {
                    name: 'Cost (' + currency + ')',
                    type: 'line',
                    data: trendData.map(t => t.cost)
                }],
                chart: {
                    height: 288,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false }
                },
                colors: ['#3b82f6', '#10b981'],
                stroke: { width: [0, 3], curve: 'smooth' },
                plotOptions: { bar: { borderRadius: 6, columnWidth: '60%' } },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: trendData.map(t => t.label),
                    labels: { style: { colors: '#64748b', fontSize: '11px' } }
                },
                yaxis: [
                    { title: { text: 'Work Orders' }, labels: { style: { colors: '#64748b' } } },
                    { opposite: true, title: { text: 'Cost (' + currency + ')' }, labels: { style: { colors: '#64748b' }, formatter: (val) => currency + val.toLocaleString() } }
                ],
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                legend: { position: 'top' }
            };

            if (document.querySelector('#trend-chart')) {
                new ApexCharts(document.querySelector('#trend-chart'), options).render();
            }
        },

        renderCostChart() {
            const facilityData = @json($data['facilityData']);
            if (facilityData.length === 0) return;
            const currency = this.currency;

            const top5 = facilityData.slice(0, 5);

            const options = {
                series: [{
                    name: 'Cost',
                    data: top5.map(f => parseFloat(f.total_cost.replace(/,/g, '')))
                }],
                chart: {
                    type: 'bar',
                    height: 288,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false }
                },
                colors: ['#14b8a6'],
                plotOptions: {
                    bar: { borderRadius: 8, horizontal: true, barHeight: '60%' }
                },
                dataLabels: {
                    enabled: true,
                    formatter: (val) => currency + val.toLocaleString(),
                    style: { fontSize: '11px' }
                },
                xaxis: {
                    categories: top5.map(f => f.name),
                    labels: { style: { colors: '#64748b', fontSize: '11px' }, formatter: (val) => currency + val.toLocaleString() }
                },
                yaxis: { labels: { style: { colors: '#64748b', fontSize: '11px' } } },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
            };

            if (document.querySelector('#cost-chart')) {
                new ApexCharts(document.querySelector('#cost-chart'), options).render();
            }
        }
    }
}
</script>

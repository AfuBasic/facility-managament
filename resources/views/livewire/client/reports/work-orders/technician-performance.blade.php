<div class="space-y-6" x-data="technicianReportCharts()">
    <!-- Back Navigation -->
    <a href="{{ route('app.reports.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-teal-600 transition-colors">
        <x-heroicon-o-arrow-left class="h-4 w-4" />
        Back to Reports
    </a>

    <!-- Page Header -->
    <x-ui.page-header title="Technician Performance Report" description="Analyze workload and completion metrics per technician.">
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
            label="Active Technicians"
            :value="$data['totalTechnicians']"
            icon="users"
            color="blue"
        />
        <x-dashboard.stat-card
            label="Total Assigned"
            :value="$data['totalAssigned']"
            icon="clipboard-document-list"
            color="indigo"
        />
        <x-dashboard.stat-card
            label="Avg Completion Rate"
            :value="$data['avgCompletionRate'] . '%'"
            icon="check-circle"
            color="emerald"
        />
        <x-dashboard.stat-card
            label="Avg SLA Rate"
            :value="$data['avgSlaRate'] . '%'"
            icon="shield-check"
            color="teal"
        />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Workload Distribution -->
        <x-ui.card>
            <h3 class="font-semibold text-slate-900 mb-4">Workload Distribution (Top 10)</h3>
            <div id="workload-chart" class="h-72"></div>
        </x-ui.card>

        <!-- Top Performers -->
        <x-ui.card>
            <h3 class="font-semibold text-slate-900 mb-4">Top Performers</h3>
            @if(count($data['topPerformers']) > 0)
                <div class="space-y-4">
                    @foreach($data['topPerformers'] as $index => $performer)
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-teal-500 to-emerald-500 flex items-center justify-center text-white font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-900 truncate">{{ $performer['name'] }}</p>
                                <p class="text-xs text-slate-500">{{ $performer['completed'] }}/{{ $performer['total_assigned'] }} completed</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <p class="text-sm font-bold text-emerald-600">{{ $performer['completion_rate'] }}%</p>
                                    <p class="text-xs text-slate-400">Completion</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-teal-600">{{ $performer['sla_rate'] }}%</p>
                                    <p class="text-xs text-slate-400">SLA</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-slate-500">
                    <x-heroicon-o-user-group class="h-10 w-10 mx-auto text-slate-300 mb-2" />
                    <p class="text-sm">No technicians with 5+ assignments</p>
                </div>
            @endif
        </x-ui.card>
    </div>

    <!-- Technician Table -->
    <x-ui.card>
        <h3 class="font-semibold text-slate-900 mb-4">All Technicians</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Technician</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Assigned</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Completed</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">In Progress</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Completion Rate</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">SLA Rate</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Avg Time</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($data['technicians'] as $tech)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-slate-600">{{ strtoupper(substr($tech['name'], 0, 2)) }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-slate-900">{{ $tech['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-slate-900">
                                {{ number_format($tech['total_assigned']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-emerald-600 font-medium">
                                {{ number_format($tech['completed']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-amber-600">
                                {{ number_format($tech['in_progress']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <div class="w-16 bg-slate-100 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $tech['completion_rate'] >= 80 ? 'bg-emerald-500' : ($tech['completion_rate'] >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $tech['completion_rate'] }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-slate-900">{{ $tech['completion_rate'] }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $tech['sla_rate'] >= 90 ? 'bg-emerald-100 text-emerald-800' : ($tech['sla_rate'] >= 70 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $tech['sla_rate'] }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-600">
                                {{ $tech['avg_completion_hours'] ? $tech['avg_completion_hours'] . 'h' : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-500">
                                No technicians found for the selected period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</div>

<script>
function technicianReportCharts() {
    return {
        init() {
            this.renderWorkloadChart();
        },

        renderWorkloadChart() {
            const workloadData = @json($data['workloadData']);

            if (workloadData.length === 0) return;

            const options = {
                series: [{
                    name: 'Assigned',
                    data: workloadData.map(w => w.value)
                }],
                chart: {
                    type: 'bar',
                    height: 288,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false }
                },
                colors: ['#14b8a6'],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 6,
                        barHeight: '60%'
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: (val) => val,
                    style: { fontSize: '11px' }
                },
                xaxis: {
                    categories: workloadData.map(w => w.name),
                    labels: { style: { colors: '#64748b', fontSize: '12px' } }
                },
                yaxis: {
                    labels: { style: { colors: '#64748b', fontSize: '12px' } }
                },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                tooltip: {
                    theme: 'light',
                    y: { formatter: (val) => val + ' work orders' }
                }
            };

            if (document.querySelector('#workload-chart')) {
                new ApexCharts(document.querySelector('#workload-chart'), options).render();
            }
        }
    }
}
</script>

<div class="space-y-6" x-data="slaReportCharts()">
    <!-- Back Navigation -->
    <a href="{{ route('app.reports.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-teal-600 transition-colors">
        <x-heroicon-o-arrow-left class="h-4 w-4" />
        Back to Reports
    </a>

    <!-- Page Header -->
    <x-ui.page-header title="SLA Compliance Report" description="Monitor response and resolution SLA performance metrics.">
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
            label="Overall Compliance"
            :value="$data['overallComplianceRate'] . '%'"
            icon="shield-check"
            :color="$data['overallComplianceRate'] >= 90 ? 'emerald' : ($data['overallComplianceRate'] >= 70 ? 'amber' : 'rose')"
        />
        <x-dashboard.stat-card
            label="Response Breached"
            :value="$data['responseBreached']"
            icon="clock"
            :color="$data['responseBreached'] > 0 ? 'rose' : 'emerald'"
        />
        <x-dashboard.stat-card
            label="Currently Overdue"
            :value="$data['currentlyOverdue']"
            icon="exclamation-triangle"
            :color="$data['currentlyOverdue'] > 0 ? 'amber' : 'emerald'"
        />
    </div>

    <!-- Compliance Gauges -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Overall Compliance Gauge -->
        <x-ui.card>
            <h3 class="font-semibold text-slate-900 mb-4 text-center">Overall Compliance</h3>
            <div class="flex items-center justify-center">
                <div class="relative w-36 h-36">
                    <svg class="w-36 h-36 transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="40" stroke="#e2e8f0" stroke-width="12" fill="none" />
                        <circle
                            cx="50" cy="50" r="40"
                            stroke="{{ $data['overallComplianceRate'] >= 90 ? '#10b981' : ($data['overallComplianceRate'] >= 70 ? '#f59e0b' : '#ef4444') }}"
                            stroke-width="12"
                            fill="none"
                            stroke-linecap="round"
                            stroke-dasharray="{{ ($data['overallComplianceRate'] / 100) * 251.2 }} 251.2"
                        />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl font-bold text-slate-900">{{ $data['overallComplianceRate'] }}%</span>
                        <span class="text-xs text-slate-500 uppercase">Compliant</span>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <!-- Response Compliance Gauge -->
        <x-ui.card>
            <h3 class="font-semibold text-slate-900 mb-4 text-center">Response SLA</h3>
            <div class="flex items-center justify-center">
                <div class="relative w-36 h-36">
                    <svg class="w-36 h-36 transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="40" stroke="#e2e8f0" stroke-width="12" fill="none" />
                        <circle
                            cx="50" cy="50" r="40"
                            stroke="{{ $data['responseComplianceRate'] >= 90 ? '#10b981' : ($data['responseComplianceRate'] >= 70 ? '#f59e0b' : '#ef4444') }}"
                            stroke-width="12"
                            fill="none"
                            stroke-linecap="round"
                            stroke-dasharray="{{ ($data['responseComplianceRate'] / 100) * 251.2 }} 251.2"
                        />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl font-bold text-slate-900">{{ $data['responseComplianceRate'] }}%</span>
                        <span class="text-xs text-slate-500 uppercase">On Time</span>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <!-- Resolution Compliance Gauge -->
        <x-ui.card>
            <h3 class="font-semibold text-slate-900 mb-4 text-center">Resolution SLA</h3>
            <div class="flex items-center justify-center">
                <div class="relative w-36 h-36">
                    <svg class="w-36 h-36 transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="40" stroke="#e2e8f0" stroke-width="12" fill="none" />
                        <circle
                            cx="50" cy="50" r="40"
                            stroke="{{ $data['resolutionComplianceRate'] >= 90 ? '#10b981' : ($data['resolutionComplianceRate'] >= 70 ? '#f59e0b' : '#ef4444') }}"
                            stroke-width="12"
                            fill="none"
                            stroke-linecap="round"
                            stroke-dasharray="{{ ($data['resolutionComplianceRate'] / 100) * 251.2 }} 251.2"
                        />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl font-bold text-slate-900">{{ $data['resolutionComplianceRate'] }}%</span>
                        <span class="text-xs text-slate-500 uppercase">Resolved</span>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>

    <!-- Trend Chart -->
    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-slate-900">Compliance Trend</h3>
            <span class="text-xs text-slate-500">{{ $this->getDateRangeLabel() }}</span>
        </div>
        <div id="compliance-trend-chart" class="h-64"></div>
    </x-ui.card>

    <!-- By Priority Table -->
    <x-ui.card>
        <h3 class="font-semibold text-slate-900 mb-4">Compliance by Priority</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Priority</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Total</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Response Breached</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Resolution Breached</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Compliance Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($data['byPriority'] as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $priorityColors = [
                                        'Low' => 'bg-emerald-100 text-emerald-800',
                                        'Medium' => 'bg-blue-100 text-blue-800',
                                        'High' => 'bg-amber-100 text-amber-800',
                                        'Critical' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$item['priority']] ?? 'bg-slate-100 text-slate-800' }}">
                                    {{ $item['priority'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-slate-900">
                                {{ number_format($item['total']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm {{ $item['response_breached'] > 0 ? 'text-red-600 font-semibold' : 'text-slate-600' }}">
                                {{ number_format($item['response_breached']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm {{ $item['resolution_breached'] > 0 ? 'text-red-600 font-semibold' : 'text-slate-600' }}">
                                {{ number_format($item['resolution_breached']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $item['compliance_rate'] >= 90 ? 'bg-emerald-100 text-emerald-800' : ($item['compliance_rate'] >= 70 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $item['compliance_rate'] }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">
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
function slaReportCharts() {
    return {
        init() {
            this.renderComplianceTrend();
        },

        renderComplianceTrend() {
            const trendData = @json($data['trendData']);

            if (trendData.length === 0) return;

            const options = {
                series: [{
                    name: 'Compliance Rate',
                    data: trendData.map(t => t.rate)
                }],
                chart: {
                    type: 'line',
                    height: 256,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                colors: ['#14b8a6'],
                dataLabels: {
                    enabled: true,
                    formatter: (val) => val + '%',
                    style: { fontSize: '10px' }
                },
                stroke: { curve: 'smooth', width: 3 },
                markers: { size: 5, colors: ['#14b8a6'], strokeColors: '#fff', strokeWidth: 2 },
                xaxis: {
                    categories: trendData.map(t => t.label),
                    labels: { style: { colors: '#94a3b8', fontSize: '11px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    min: 0,
                    max: 100,
                    labels: {
                        style: { colors: '#94a3b8', fontSize: '11px' },
                        formatter: (val) => val + '%'
                    }
                },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4, padding: { left: 10, right: 10 } },
                tooltip: {
                    theme: 'light',
                    y: { formatter: (val) => val + '% compliant' }
                },
                annotations: {
                    yaxis: [{
                        y: 90,
                        borderColor: '#10b981',
                        strokeDashArray: 4,
                        label: {
                            text: 'Target (90%)',
                            style: { color: '#10b981', fontSize: '10px' }
                        }
                    }]
                }
            };

            if (document.querySelector('#compliance-trend-chart')) {
                new ApexCharts(document.querySelector('#compliance-trend-chart'), options).render();
            }
        }
    }
}
</script>

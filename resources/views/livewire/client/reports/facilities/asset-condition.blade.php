<div class="space-y-6" x-data="assetReportCharts()">
    <!-- Back Navigation -->
    <a href="{{ route('app.reports.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-teal-600 transition-colors">
        <x-heroicon-o-arrow-left class="h-4 w-4" />
        Back to Reports
    </a>

    <!-- Page Header -->
    <x-ui.page-header title="Asset Condition Report" description="Asset status and distribution across facilities.">
        <x-slot:actions>
            <x-reports.export-buttons />
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Filters -->
    <x-ui.card>
        <div class="flex flex-wrap items-end gap-4">
            <div class="w-56">
                <x-forms.searchable-select
                    wire:model.live="facilityId"
                    :options="$this->facilities->toArray()"
                    :selected="$facilityId"
                    placeholder="All Facilities"
                    label="Facility"
                />
            </div>
            <div class="w-56">
                <x-forms.searchable-select
                    wire:model.live="assetType"
                    :options="collect($this->assetTypes)->mapWithKeys(fn($type) => [$type => ucfirst($type)])->toArray()"
                    :selected="$assetType"
                    placeholder="All Types"
                    label="Asset Type"
                />
            </div>
        </div>
    </x-ui.card>



    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <x-dashboard.stat-card
            label="Total Assets"
            :value="$data['summary']['total']"
            icon="cube"
            color="blue"
        />
        <x-dashboard.stat-card
            label="Available"
            :value="$data['summary']['available']"
            icon="check-circle"
            color="emerald"
        />
        <x-dashboard.stat-card
            label="Checked Out"
            :value="$data['summary']['checked_out']"
            icon="arrow-right-circle"
            color="amber"
        />
        <x-dashboard.stat-card
            label="Asset Types"
            :value="$data['summary']['types_count']"
            icon="tag"
            color="indigo"
        />
        <x-dashboard.stat-card
            label="Facilities"
            :value="$data['summary']['facilities_count']"
            icon="building-office-2"
            color="teal"
        />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Assets by Type -->
        <x-ui.card>
            <h3 class="font-semibold text-slate-900 mb-4">Assets by Type</h3>
            <div id="type-chart" class="h-72"></div>
        </x-ui.card>

        <!-- Assets by Facility -->
        <x-ui.card>
            <h3 class="font-semibold text-slate-900 mb-4">Assets by Facility</h3>
            <div id="facility-chart" class="h-72"></div>
        </x-ui.card>
    </div>

    <!-- Facility Distribution Table -->
    <x-ui.card>
        <h3 class="font-semibold text-slate-900 mb-4">Facility Distribution</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Facility</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Total</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Available</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Checked Out</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Utilization</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($data['byFacility'] as $facility)
                        @php
                            $utilization = $facility['total'] > 0 ? round(($facility['checked_out'] / $facility['total']) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-slate-900">{{ $facility['facility'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-slate-900">
                                {{ number_format($facility['total']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-emerald-600">
                                {{ number_format($facility['available']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-amber-600">
                                {{ number_format($facility['checked_out']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-slate-100 rounded-full h-2 max-w-[100px]">
                                        <div class="h-2 rounded-full {{ $utilization > 70 ? 'bg-amber-500' : 'bg-teal-500' }}" style="width: {{ $utilization }}%"></div>
                                    </div>
                                    <span class="text-xs text-slate-600">{{ $utilization }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                No assets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <!-- Asset Details Table -->
    <x-ui.card>
        <h3 class="font-semibold text-slate-900 mb-4">Asset Details</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Serial</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Facility</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Assigned To</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($data['assetList'] as $asset)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-slate-900">{{ $asset['name'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $asset['serial'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $asset['type'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $asset['facility'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $asset['status'] === 'Available' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $asset['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $asset['assigned_to'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                No assets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</div>

<script>
function assetReportCharts() {
    return {
        init() {
            this.renderTypeChart();
            this.renderFacilityChart();
        },

        renderTypeChart() {
            const typeData = @json($data['byType']);
            if (typeData.length === 0) return;

            const options = {
                series: typeData.map(t => t.count),
                labels: typeData.map(t => t.type),
                chart: {
                    type: 'donut',
                    height: 288,
                    fontFamily: 'Inter, sans-serif',
                },
                colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316', '#84cc16'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
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
                legend: { position: 'bottom', fontSize: '11px' },
                stroke: { show: false }
            };

            if (document.querySelector('#type-chart')) {
                new ApexCharts(document.querySelector('#type-chart'), options).render();
            }
        },

        renderFacilityChart() {
            const facilityData = @json($data['byFacility']);
            if (facilityData.length === 0) return;

            const top5 = facilityData.slice(0, 5);

            const options = {
                series: [{
                    name: 'Available',
                    data: top5.map(f => f.available)
                }, {
                    name: 'Checked Out',
                    data: top5.map(f => f.checked_out)
                }],
                chart: {
                    type: 'bar',
                    height: 288,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false },
                    stacked: true
                },
                colors: ['#10b981', '#f59e0b'],
                plotOptions: {
                    bar: { borderRadius: 6, horizontal: true, barHeight: '60%' }
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: top5.map(f => f.facility),
                    labels: { style: { colors: '#64748b', fontSize: '11px' } }
                },
                yaxis: { labels: { style: { colors: '#64748b', fontSize: '11px' } } },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                legend: { position: 'top' }
            };

            if (document.querySelector('#facility-chart')) {
                new ApexCharts(document.querySelector('#facility-chart'), options).render();
            }
        }
    }
}
</script>

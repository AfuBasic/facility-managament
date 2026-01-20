<div class="space-y-8">
    <!-- Page Header -->
    <x-ui.page-header title="Reports" description="Generate and export comprehensive reports for your facility management data.">
        <x-slot:actions>
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <x-heroicon-o-information-circle class="h-5 w-5" />
                <span>Select a report to view and export data</span>
            </div>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Report Categories -->
    <div class="space-y-8">
        @foreach($categories as $category)
            <div>
                <!-- Category Header -->
                <div class="flex items-center gap-3 mb-4">
                    @php
                        $colorClasses = [
                            'blue' => 'bg-blue-50 text-blue-600',
                            'teal' => 'bg-teal-50 text-teal-600',
                            'emerald' => 'bg-emerald-50 text-emerald-600',
                        ];
                        $colorClass = $colorClasses[$category['color']] ?? 'bg-slate-50 text-slate-600';
                    @endphp
                    <div class="p-2 rounded-lg {{ $colorClass }}">
                        <x-dynamic-component :component="'heroicon-o-' . $category['icon']" class="h-5 w-5" />
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ $category['title'] }}</h2>
                        <p class="text-sm text-slate-500">{{ $category['description'] }}</p>
                    </div>
                </div>

                <!-- Report Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($category['reports'] as $report)
                        <a
                            href="{{ route($report['route']) }}"
                            wire:navigate
                            class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-teal-300 hover:shadow-lg transition-all duration-200"
                        >
                            <div class="flex items-start gap-4">
                                <div class="p-3 rounded-xl bg-slate-100 text-slate-600 group-hover:bg-teal-50 group-hover:text-teal-600 transition-colors">
                                    <x-dynamic-component :component="'heroicon-o-' . $report['icon']" class="h-6 w-6" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-slate-900 group-hover:text-teal-700 transition-colors">
                                        {{ $report['name'] }}
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $report['description'] }}
                                    </p>
                                </div>
                                <x-heroicon-o-arrow-right class="h-5 w-5 text-slate-300 group-hover:text-teal-500 group-hover:translate-x-1 transition-all" />
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- Quick Tips -->
    <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-2xl p-6 border border-slate-200">
        <div class="flex items-start gap-4">
            <div class="p-3 rounded-xl bg-white shadow-sm">
                <x-heroicon-o-light-bulb class="h-6 w-6 text-amber-500" />
            </div>
            <div>
                <h3 class="font-semibold text-slate-900">Quick Tips</h3>
                <ul class="mt-2 text-sm text-slate-600 space-y-1">
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-teal-500" />
                        Use date range filters to narrow down your analysis
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-teal-500" />
                        Export reports to PDF for presentations or Excel for further analysis
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="h-4 w-4 text-teal-500" />
                        Share report URLs to collaborate with your team
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

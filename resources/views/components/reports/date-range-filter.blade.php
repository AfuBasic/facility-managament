@props(['presets' => [], 'dateRange' => 'last_30_days', 'startDate' => null, 'endDate' => null])

<div class="flex flex-col sm:flex-row gap-4">
    <!-- Preset Dropdown -->
    <div class="flex-1 min-w-[180px]">
        <x-forms.searchable-select
            wire:model.live="dateRange"
            :options="$presets"
            :selected="$dateRange"
            placeholder="Select date range"
            label="Date Range"
        />
    </div>

    <!-- Custom Date Inputs (shown when "custom" is selected) -->
    @if($dateRange === 'custom')
        <div class="flex-1 min-w-[160px]" x-data="datePickerStart()" wire:ignore>
            <label class="block text-sm font-medium text-slate-700 mb-2">Start Date</label>
            <div class="relative">
                <input
                    x-ref="startPicker"
                    type="text"
                    placeholder="Select start date"
                    readonly
                    class="w-full rounded-xl border border-slate-300 bg-white pl-10 pr-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:outline-none cursor-pointer transition-colors"
                />
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <x-heroicon-o-calendar class="h-4 w-4 text-slate-400" />
                </div>
            </div>
        </div>
        <div class="flex-1 min-w-[160px]" x-data="datePickerEnd()" wire:ignore>
            <label class="block text-sm font-medium text-slate-700 mb-2">End Date</label>
            <div class="relative">
                <input
                    x-ref="endPicker"
                    type="text"
                    placeholder="Select end date"
                    readonly
                    class="w-full rounded-xl border border-slate-300 bg-white pl-10 pr-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:outline-none cursor-pointer transition-colors"
                />
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <x-heroicon-o-calendar class="h-4 w-4 text-slate-400" />
                </div>
            </div>
        </div>

        <script>
            function datePickerStart() {
                return {
                    init() {
                        flatpickr(this.$refs.startPicker, {
                            dateFormat: 'Y-m-d',
                            defaultDate: @json($startDate),
                            onChange: (selectedDates, dateStr) => {
                                @this.set('startDate', dateStr);
                            }
                        });
                    }
                }
            }
            function datePickerEnd() {
                return {
                    init() {
                        flatpickr(this.$refs.endPicker, {
                            dateFormat: 'Y-m-d',
                            defaultDate: @json($endDate),
                            onChange: (selectedDates, dateStr) => {
                                @this.set('endDate', dateStr);
                            }
                        });
                    }
                }
            }
        </script>
    @endif
</div>

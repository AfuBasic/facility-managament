@props(['pdfAction' => 'exportPdf', 'excelAction' => 'exportExcel'])

<div class="flex items-center gap-2">
    <button
        wire:click="{{ $pdfAction }}"
        wire:loading.attr="disabled"
        wire:target="{{ $pdfAction }}"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-700 hover:border-rose-300 hover:text-rose-700 hover:shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed"
    >
        <span wire:loading.remove wire:target="{{ $pdfAction }}">
            <x-heroicon-o-document-arrow-down class="h-4 w-4" />
        </span>
        <span wire:loading wire:target="{{ $pdfAction }}">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </span>
        <span>Export PDF</span>
    </button>

    <button
        wire:click="{{ $excelAction }}"
        wire:loading.attr="disabled"
        wire:target="{{ $excelAction }}"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-700 hover:border-emerald-300 hover:text-emerald-700 hover:shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed"
    >
        <span wire:loading.remove wire:target="{{ $excelAction }}">
            <x-heroicon-o-table-cells class="h-4 w-4" />
        </span>
        <span wire:loading wire:target="{{ $excelAction }}">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </span>
        <span>Export Excel</span>
    </button>
</div>

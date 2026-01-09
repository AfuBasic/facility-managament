{{-- Tab Navigation --}}
<div class="border-b border-slate-200 overflow-x-auto">
    <nav class="flex gap-4 sm:gap-8 px-4 sm:px-6 min-w-max sm:min-w-0" aria-label="Tabs">
        @can('view spaces')
            <button 
                wire:click="setTab('spaces')" 
                class="py-3 sm:py-4 px-2 sm:px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'spaces' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}"
            >
                Spaces
            </button>
        @endcan
        
        @can('view stores')
            <button 
                wire:click="setTab('stores')" 
                class="py-3 sm:py-4 px-2 sm:px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'stores' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}"
            >
                Stores
            </button>
        @endcan
        
        @can('view facility_managers')
            <button 
                wire:click="setTab('managers')" 
                class="py-3 sm:py-4 px-2 sm:px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'managers' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}"
            >
                Managers
            </button>
        @endcan
    </nav>
</div>

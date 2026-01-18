<div x-data="{ showSettingsModal: false, selectedClientId: null }" @settings-close.window="showSettingsModal = false">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Your Clients</h1>
        <p class="text-slate-500 mt-2">Select a Client to manage or view details.</p>
    </div>

    @if($memberships->count() > 0)
        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($memberships as $membership)
                <div class="group relative bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-lg hover:border-teal-200 transition-all duration-300">
                    
                    <!-- Card Top Decor -->
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-teal-400 to-emerald-500 rounded-t-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="flex items-start justify-between mb-4">
                        <div class="h-12 w-12 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 group-hover:bg-teal-100 transition-colors">
                            <x-heroicon-o-building-office-2 class="w-6 h-6" />
                        </div>
                        
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $membership->status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ ucfirst($membership->status) }}
                        </span>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-900 group-hover:text-teal-900 transition-colors">
                        {{ $membership->clientAccount->name }}
                    </h3>
                    
                    <p class="text-sm text-slate-500 mt-1 mb-6">
                        Member since {{ $membership->created_at->format('M Y') }}
                    </p>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('app.switch', ['client_id' => $membership->clientAccount->id]) }}" 
                           class="flex-1 flex items-center justify-center px-4 py-2 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 hover:text-slate-900 group-hover:border-teal-200 group-hover:text-teal-700 transition-all">
                            <span>Manage</span>
                            <x-heroicon-o-arrow-right class="ml-2 h-4 w-4 transform group-hover:translate-x-1 transition-transform" />
                        </a>
                        @if($membership->canModify(auth()->user()))
                        <button @click="selectedClientId = {{ $membership->clientAccount->id }}; showSettingsModal = true" 
                                type="button" 
                                class="p-2 text-slate-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-colors border border-slate-200" 
                                title="Company Settings">
                            <x-heroicon-o-cog-6-tooth class="h-5 w-5" />
                        </button>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

    @else
        <!-- Empty State -->
        <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-300">
            <div class="mx-auto h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                <x-heroicon-o-document class="w-8 h-8 text-slate-400" />
            </div>
            <h3 class="text-lg font-medium text-slate-900">No organizations found</h3>
            <p class="text-slate-500 mt-1 text-sm">You are not a member of any organizations yet.</p>
        </div>
    @endif

    <!-- Settings Modal (Outside loop, single instance) -->
    <div x-show="showSettingsModal" 
         x-cloak 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop with blur -->
        <div class="fixed inset-0 bg-slate-900/30 backdrop-blur-sm" @click="showSettingsModal = false"></div>
        
        <!-- Modal Content -->
        <div x-show="showSettingsModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
            
            <template x-if="selectedClientId">
                <div wire:ignore>
                    @foreach($memberships as $membership)
                        <div x-show="selectedClientId === {{ $membership->clientAccount->id }}">
                            <livewire:client.settings 
                                :client-account-id="$membership->clientAccount->id" 
                                :key="'settings-'.$membership->clientAccount->id"
                                @saved="showSettingsModal = false" />
                        </div>
                    @endforeach
                </div>
            </template>
        </div>
    </div>
</div>

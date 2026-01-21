<div x-data="{ showSettingsModal: false, selectedClientId: null }" @settings-close.window="showSettingsModal = false">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Your Organizations</h1>
            <p class="text-slate-500 mt-2">Select an organization to manage or create a new one.</p>
        </div>
        <button 
            wire:click="openCreateModal" 
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 text-white font-semibold rounded-xl shadow-md hover:from-teal-600 hover:to-emerald-600 hover:shadow-lg transition-all"
        >
            <x-heroicon-o-plus class="w-5 h-5" />
            New Organization
        </button>
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
    <!-- Settings Modal -->
    <x-ui.modal x-model="showSettingsModal" title="Company Settings" maxWidth="lg">
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
    </x-ui.modal>

    <!-- Create Organization Modal -->
    <x-ui.modal wire:model="showCreateModal" title="Create New Organization" maxWidth="md">
        <form wire:submit="createOrganization">
            <div class="space-y-5">
                <!-- Organization Name -->
                <div>
                    <label for="orgName" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Organization Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model="orgName" 
                        id="orgName"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors"
                        placeholder="Enter organization name"
                    >
                    @error('orgName')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notification Email -->
                <div>
                    <label for="orgEmail" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Notification Email
                    </label>
                    <input 
                        type="email" 
                        wire:model="orgEmail" 
                        id="orgEmail"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors"
                        placeholder="notifications@company.com"
                    >
                    @error('orgEmail')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="orgPhone" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Company Phone
                    </label>
                    <input 
                        type="text" 
                        wire:model="orgPhone" 
                        id="orgPhone"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors"
                        placeholder="+234 800 000 0000"
                    >
                    @error('orgPhone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label for="orgAddress" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Address
                    </label>
                    <textarea 
                        wire:model="orgAddress" 
                        id="orgAddress"
                        rows="2"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors resize-none"
                        placeholder="Enter company address"
                    ></textarea>
                    @error('orgAddress')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Info Notice -->
            <div class="mt-5 p-3 bg-teal-50 rounded-lg border border-teal-100">
                <p class="text-sm text-teal-800 flex items-start gap-2">
                    <x-heroicon-o-information-circle class="w-5 h-5 flex-shrink-0 mt-0.5" />
                    <span>You will become the admin of this organization and can invite other members.</span>
                </p>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end gap-3">
                <button 
                    type="button" 
                    wire:click="$set('showCreateModal', false)"
                    class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-5 py-2 text-sm font-medium text-white bg-gradient-to-r from-teal-500 to-emerald-500 rounded-lg hover:from-teal-600 hover:to-emerald-600 shadow-sm transition-all"
                >
                    <span wire:loading.remove wire:target="createOrganization">Create Organization</span>
                    <span wire:loading wire:target="createOrganization">Creating...</span>
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>

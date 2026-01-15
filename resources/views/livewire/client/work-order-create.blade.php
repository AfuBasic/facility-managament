<div>
    <x-ui.page-header 
        title="Create Work Order" 
        description="Report a new facility maintenance issue."
    />

    <x-ui.card>
        <form wire:submit="submit" class="space-y-6">
            {{-- Title --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Title *</label>
                <input wire:model="title" type="text" 
                    class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500"
                    placeholder="Brief description of the issue">
                @error('title') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description *</label>
                <textarea wire:model="description" rows="4"
                    class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500"
                    placeholder="Detailed description of the problem"></textarea>
                @error('description') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            {{-- Priority --}}
            <div>
                <x-forms.searchable-select
                    wire:model="priority"
                    :options="[
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'critical' => 'Critical'
                    ]"
                    :selected="$priority"
                    label="Priority *"
                    placeholder="Select priority..."
                />
                @error('priority') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            {{-- Facility --}}
            <div>
                <x-forms.searchable-select
                    wire:model.live="facility_id"
                    :options="$this->facilities"
                    :selected="$facility_id"
                    label="Facility *"
                    placeholder="Select facility..."
                />
                @error('facility_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            {{-- Space (Optional) --}}
            @if($facility_id && $this->spaces->isNotEmpty())
                <div>
                    <x-forms.searchable-select
                        wire:model="space_id"
                        :options="$this->spaces"
                        :selected="$space_id"
                        label="Space (Optional)"
                        placeholder="Select space..."
                    />
                    @error('space_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>
            @endif

            {{-- Asset (Optional) --}}
            @if($facility_id && $this->assets->isNotEmpty())
                <div>
                    <x-forms.searchable-select
                        wire:model="asset_id"
                        :options="$this->assets"
                        :selected="$asset_id"
                        label="Related Asset (Optional)"
                        placeholder="Select asset..."
                    />
                    @error('asset_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('app.work-orders.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 focus:ring-teal-500">
                    Cancel
                </a>
                <x-ui.button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submit">Submit Work Order</span>
                    <span wire:loading wire:target="submit">Submitting...</span>
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-slate-900">Company Settings</h3>
    </div>

    <form wire:submit="save" class="space-y-4">
        <!-- Company Name -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Company Name</label>
            <input type="text" wire:model="name" class="w-full rounded-lg border border-slate-200 p-2.5 focus:border-teal-500 focus:ring-teal-500 transition-colors" placeholder="Your company name">
            @error('name') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Notification Email -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Notification Email</label>
            <input type="email" wire:model="notificationEmail" class="w-full rounded-lg border border-slate-200 p-2.5 focus:border-teal-500 focus:ring-teal-500 transition-colors" placeholder="notifications@company.com">
            <p class="text-xs text-slate-400 mt-1">Receives SLA breach alerts for unassigned work orders.</p>
            @error('notificationEmail') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Company Phone -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Company Phone</label>
            <input type="text" wire:model="companyPhone" class="w-full rounded-lg border border-slate-200 p-2.5 focus:border-teal-500 focus:ring-teal-500 transition-colors" placeholder="+234 801 234 5678">
            @error('companyPhone') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Currency -->
        <div>
            <x-forms.searchable-select
                wire:model="currency"
                :options="$this->currencyOptions"
                :selected="$currency"
                label="Currency"
                placeholder="Select a currency..."
                :error="$errors->first('currency')"
            />
            <p class="text-xs text-slate-400 mt-1">Currency symbol displayed throughout the application.</p>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <button type="button" @click="$dispatch('settings-close')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Cancel
            </button>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition-colors" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save Changes</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>

<div class="p-2 space-y-2 md:p-6 md:space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Contact Types</h1>
            <p class="text-sm text-slate-600 mt-1">Manage contact type categories</p>
        </div>
        @can('create contacts')
            <button wire:click="create" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all">
                <x-heroicon-o-plus class="h-4 w-4" />
                Add Type
            </button>
        @endcan
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        {{-- Search Bar --}}
        <div class="p-4 border-b border-slate-200">
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Search types..." 
                class="w-full md:w-96 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
            />
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($types as $type)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                {{ $type->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button 
                                    wire:click="toggleStatus({{ $type->id }})"
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $type->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-800' }}"
                                >
                                    {{ ucfirst($type->status) }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $type->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @can('edit contacts')
                                        <button wire:click="edit({{ $type->id }})" class="text-teal-600 hover:text-teal-900">
                                            Edit
                                        </button>
                                    @endcan
                                    @can('delete contacts')
                                        <button 
                                            @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                                                detail: {
                                                    title: 'Delete Contact Type',
                                                    message: 'Are you sure you want to delete this type?',
                                                    confirmText: 'Delete',
                                                    cancelText: 'Cancel',
                                                    variant: 'danger',
                                                    action: () => $wire.delete({{ $type->id }})
                                                }
                                            }))"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">
                                No contact types found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($types->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $types->links() }}
            </div>
        @endif
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

                <div class="relative inline-block align-bottom bg-white rounded-2xl border border-slate-200 px-6 pt-5 pb-6 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-8">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-slate-900">
                                {{ $isEditing ? 'Edit Contact Type' : 'Create Contact Type' }}
                            </h3>
                            <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                                <x-heroicon-o-x-mark class="h-6 w-6" />
                            </button>
                        </div>

                        <form wire:submit="save" class="space-y-5">
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                                    Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    wire:model="name" 
                                    type="text" 
                                    id="name"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                                    placeholder="e.g., Vendor, Customer"
                                />
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-slate-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    wire:model="status" 
                                    id="status"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex items-center gap-3 pt-4">
                                <button 
                                    type="submit"
                                    class="flex-1 inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2.5 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all"
                                >
                                    {{ $isEditing ? 'Update Type' : 'Create Type' }}
                                </button>
                                <button 
                                    type="button"
                                    wire:click="closeModal"
                                    class="px-4 py-2.5 text-sm font-medium text-slate-700 hover:text-slate-900 transition-colors"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

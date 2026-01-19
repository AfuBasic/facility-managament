<div class="p-2 space-y-2 md:p-6 md:space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">All Contacts</h1>
            <p class="text-sm text-slate-600 mt-1">Manage your contacts database</p>
        </div>
        @can('create contacts')
            <button wire:click="create" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all">
                <x-heroicon-o-plus class="h-4 w-4" />
                Add Contact
            </button>
        @endcan
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        {{-- Search and Filters --}}
        <div class="p-4 border-b border-slate-200 space-y-4">
            <div class="flex flex-col md:flex-row gap-4">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search by name, email, or phone..."
                    class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                />
                <select
                    wire:model.live="filterType"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                >
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                <select
                    wire:model.live="filterGroup"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                >
                    <option value="">All Groups</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Contact Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Group</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($contacts as $contact)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-teal-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-teal-700">
                                                {{ substr($contact->firstname, 0, 1) }}{{ substr($contact->lastname, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-slate-900">{{ $contact->full_name }}</div>
                                        @if($contact->contactPerson)
                                            <div class="text-xs text-slate-500">Contact: {{ $contact->contactPerson->full_name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-900">{{ $contact->email ?: '-' }}</div>
                                <div class="text-sm text-slate-500">{{ $contact->phone ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($contact->contactType)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $contact->contactType->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($contact->contactGroup)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $contact->contactGroup->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @can('edit contacts')
                                        <button wire:click="edit({{ $contact->id }})" class="text-teal-600 hover:text-teal-900">
                                            Edit
                                        </button>
                                    @endcan
                                    @can('delete contacts')
                                        <button
                                            @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                                                detail: {
                                                    title: 'Delete Contact',
                                                    message: 'Are you sure you want to delete this contact?',
                                                    confirmText: 'Delete',
                                                    cancelText: 'Cancel',
                                                    variant: 'danger',
                                                    action: () => $wire.delete({{ $contact->id }})
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
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                                No contacts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($contacts->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $contacts->links() }}
            </div>
        @endif
    </div>

    {{-- Modal --}}
    {{-- Modal --}}
    <x-ui.modal show="showModal" title="{{ $isEditing ? 'Edit Contact' : 'Create New Contact' }}" maxWidth="3xl">
        <form wire:submit="save" class="space-y-5">
            {{-- Personal Information --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="firstname" class="block text-sm font-medium text-slate-700 mb-2">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        wire:model="firstname"
                        type="text"
                        id="firstname"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                        placeholder="John"
                    />
                    @error('firstname') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="lastname" class="block text-sm font-medium text-slate-700 mb-2">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        wire:model="lastname"
                        type="text"
                        id="lastname"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                        placeholder="Doe"
                    />
                    @error('lastname') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input
                        wire:model="email"
                        type="email"
                        id="email"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                        placeholder="john@example.com"
                    />
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-2">
                        Phone <span class="text-red-500">*</span>
                    </label>
                    <input
                        wire:model="phone"
                        type="text"
                        id="phone"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                        placeholder="+1234567890"
                    />
                    @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Additional Details --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="birthday" class="block text-sm font-medium text-slate-700 mb-2">
                        Birthday
                    </label>
                    <input
                        wire:model="birthday"
                        type="date"
                        id="birthday"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                    />
                    @error('birthday') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-slate-700 mb-2">
                        Gender <span class="text-red-500">*</span>
                    </label>
                    <x-forms.searchable-select
                        wire:model="gender"
                        id="gender"
                        :options="['male' => 'Male', 'female' => 'Female', 'other' => 'Other']"
                        placeholder="Select..."
                        :error="$errors->first('gender')"
                    />
                </div>
            </div>

            {{-- Categorization --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="contact_type_id" class="block text-sm font-medium text-slate-700">
                            Contact Type <span class="text-red-500">*</span>
                        </label>
                        <button type="button" wire:click="toggleCreateType" class="text-xs text-teal-600 hover:text-teal-700 font-medium">
                            {{ $isCreatingType ? 'Select Existing' : '+ Create New' }}
                        </button>
                    </div>
                    
                    @if($isCreatingType)
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <input
                                    wire:model="newTypeName"
                                    type="text"
                                    placeholder="Enter type name"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                                />
                                @error('newTypeName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <button type="button" wire:click="saveType" class="px-3 py-2 bg-teal-600 text-white rounded-lg text-sm hover:bg-teal-700">
                                Save
                            </button>
                        </div>
                    @else
                        <x-forms.searchable-select
                            wire:model="contact_type_id"
                            id="contact_type_id"
                            :options="$types->pluck('name', 'id')->toArray()"
                            placeholder="Select..."
                            :error="$errors->first('contact_type_id')"
                        />
                    @endif
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="contact_group_id" class="block text-sm font-medium text-slate-700">
                            Contact Group <span class="text-red-500">*</span>
                        </label>
                        <button type="button" wire:click="toggleCreateGroup" class="text-xs text-teal-600 hover:text-teal-700 font-medium">
                            {{ $isCreatingGroup ? 'Select Existing' : '+ Create New' }}
                        </button>
                    </div>

                    @if($isCreatingGroup)
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <input
                                    wire:model="newGroupName"
                                    type="text"
                                    placeholder="Enter group name"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                                />
                                @error('newGroupName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <button type="button" wire:click="saveGroup" class="px-3 py-2 bg-teal-600 text-white rounded-lg text-sm hover:bg-teal-700">
                                Save
                            </button>
                        </div>
                    @else
                        <x-forms.searchable-select
                            wire:model="contact_group_id"
                            id="contact_group_id"
                            :options="$groups->pluck('name', 'id')->toArray()"
                            placeholder="Select..."
                            :error="$errors->first('contact_group_id')"
                        />
                    @endif
                </div>
            </div>

            {{-- Contact Person --}}
            <div>
                <label for="contact_person_id" class="block text-sm font-medium text-slate-700 mb-2">
                    Contact Person
                </label>
                <x-forms.searchable-select
                    wire:model="contact_person_id"
                    :options="$availableContacts->pluck('full_name', 'id')->toArray()"
                    :selected="$contact_person_id"
                    placeholder="Select a contact person..."
                    :error="$errors->first('contact_person_id')"
                />
            </div>

            {{-- Address --}}
            <div>
                <label for="address" class="block text-sm font-medium text-slate-700 mb-2">
                    Address
                </label>
                <textarea
                    wire:model="address"
                    id="address"
                    rows="2"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 resize-none"
                    placeholder="Enter address..."
                ></textarea>
                @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Notes --}}
            <div>
                <label for="notes" class="block text-sm font-medium text-slate-700 mb-2">
                    Notes
                </label>
                <textarea
                    wire:model="notes"
                    id="notes"
                    rows="3"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 resize-none"
                    placeholder="Additional notes..."
                ></textarea>
                @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button
                    type="submit"
                    class="flex-1 inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2.5 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all"
                >
                    {{ $isEditing ? 'Update Contact' : 'Create Contact' }}
                </button>
                <button
                    type="button"
                    @click="show = false"
                    class="px-4 py-2.5 text-sm font-medium text-slate-700 hover:text-slate-900 transition-colors"
                >
                    Cancel
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>

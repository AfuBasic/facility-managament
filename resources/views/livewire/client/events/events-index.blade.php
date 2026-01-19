<div class="p-2 space-y-2 md:p-6 md:space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Events</h1>
            <p class="text-sm text-slate-600 mt-1">Schedule and manage meetings with your contacts</p>
        </div>
        <button wire:click="create" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all">
            <x-heroicon-o-plus class="h-4 w-4" />
            New Event
        </button>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        {{-- Search and Filters --}}
        <div class="p-4 border-b border-slate-200">
            <div class="flex flex-col md:flex-row gap-4">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search events..."
                    class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                />
                <x-forms.searchable-select
                    wire:model.live="filter"
                    :options="['upcoming' => 'Upcoming', 'past' => 'Past', 'all' => 'All Events']"
                    :selected="$filter"
                    placeholder="Filter events..."
                />
            </div>
        </div>

        {{-- Events List --}}
        <div class="divide-y divide-slate-200">
            @forelse($this->events as $event)
                <div wire:key="event-{{ $event->id }}" class="p-6 hover:bg-slate-50 transition-colors">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                        {{-- Date Badge --}}
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 flex flex-col items-center justify-center text-white shadow-sm">
                                <span class="text-xs font-medium uppercase">{{ $event->starts_at->format('M') }}</span>
                                <span class="text-2xl font-bold leading-none">{{ $event->starts_at->format('d') }}</span>
                            </div>
                        </div>

                        {{-- Event Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">{{ $event->title }}</h3>
                                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-slate-500">
                                        <span class="inline-flex items-center gap-1">
                                            <x-heroicon-o-clock class="h-4 w-4" />
                                            {{ $event->formatted_time }}
                                        </span>
                                        <span class="inline-flex items-center gap-1">
                                            @if($event->isVirtual())
                                                <x-heroicon-o-video-camera class="h-4 w-4" />
                                                Virtual Meeting
                                            @else
                                                <x-heroicon-o-map-pin class="h-4 w-4" />
                                                {{ Str::limit($event->location, 30) }}
                                            @endif
                                        </span>
                                        @if($event->attendees->count() > 0)
                                            <span class="inline-flex items-center gap-1">
                                                <x-heroicon-o-users class="h-4 w-4" />
                                                {{ $event->attendees->count() }} {{ Str::plural('attendee', $event->attendees->count()) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Status Badge --}}
                                <div class="flex-shrink-0">
                                    @if($event->isUpcoming())
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-800">
                                            Upcoming
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-slate-100 text-slate-600">
                                            Past
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Attendees List --}}
                            @if($event->attendees->count() > 0)
                                <div class="mt-2 flex items-center gap-1">
                                    <div class="flex -space-x-2">
                                        @foreach($event->attendees->take(5) as $attendee)
                                            <div class="h-7 w-7 rounded-full bg-teal-100 border-2 border-white flex items-center justify-center text-xs font-medium text-teal-700" title="{{ $attendee->name }}">
                                                {{ substr($attendee->name, 0, 1) }}
                                            </div>
                                        @endforeach
                                        @if($event->attendees->count() > 5)
                                            <div class="h-7 w-7 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-xs font-medium text-slate-600">
                                                +{{ $event->attendees->count() - 5 }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-xs text-slate-500 ml-2">
                                        {{ $event->attendees->take(2)->pluck('name')->join(', ') }}{{ $event->attendees->count() > 2 ? '...' : '' }}
                                    </span>
                                </div>
                            @endif

                            @if($event->description)
                                <p class="mt-2 text-sm text-slate-600 line-clamp-2">{{ $event->description }}</p>
                            @endif

                            {{-- Meeting Link --}}
                            @if($event->isVirtual() && $event->meeting_link)
                                <div class="mt-3">
                                    <a href="{{ $event->meeting_link }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 text-sm text-teal-600 hover:text-teal-700 font-medium">
                                        <x-heroicon-o-video-camera class="h-4 w-4" />
                                        Join Meeting
                                        <x-heroicon-o-arrow-top-right-on-square class="h-3 w-3" />
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 lg:flex-shrink-0">
                            @if($event->isUpcoming())
                                <button
                                    wire:click="resendInvitation({{ $event->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-slate-600 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-colors"
                                    title="Resend invitation"
                                >
                                    <x-heroicon-o-envelope class="h-4 w-4" />
                                    <span class="hidden sm:inline">Resend</span>
                                </button>
                            @endif
                            <button
                                wire:click="edit({{ $event->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-slate-600 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-colors"
                            >
                                <x-heroicon-o-pencil class="h-4 w-4" />
                                <span class="hidden sm:inline">Edit</span>
                            </button>
                            <button
                                @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                                    detail: {
                                        title: 'Delete Event',
                                        message: 'Are you sure you want to delete this event?',
                                        confirmText: 'Delete',
                                        cancelText: 'Cancel',
                                        variant: 'danger',
                                        action: () => $wire.deleteEvent({{ $event->id }})
                                    }
                                }))"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                            >
                                <x-heroicon-o-trash class="h-4 w-4" />
                                <span class="hidden sm:inline">Delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                        <x-heroicon-o-calendar class="h-8 w-8 text-slate-400" />
                    </div>
                    <h3 class="text-lg font-medium text-slate-900 mb-1">No events found</h3>
                    <p class="text-sm text-slate-500">
                        @if($filter === 'upcoming')
                            You don't have any upcoming events scheduled.
                        @elseif($filter === 'past')
                            No past events found.
                        @else
                            Get started by creating your first event.
                        @endif
                    </p>
                    @if(!$search)
                        <button wire:click="create" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-teal-600 hover:text-teal-700 hover:bg-teal-50 rounded-lg transition-colors">
                            <x-heroicon-o-plus class="h-4 w-4" />
                            Create Event
                        </button>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($this->events->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $this->events->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    <template x-teleport="body">
        <div
            x-data="{ show: $wire.entangle('showModal') }"
            x-show="show"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm"
                    @click="show = false"
                ></div>

                {{-- Modal Panel --}}
                <div
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-bottom bg-white rounded-2xl border border-slate-200 px-6 pt-5 pb-6 text-left overflow-hidden shadow-2xl transform sm:my-8 sm:align-middle sm:max-w-xl sm:w-full sm:p-8"
                >
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-slate-900">
                                {{ $isEditing ? 'Edit Event' : 'Create New Event' }}
                            </h3>
                            <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                                <x-heroicon-o-x-mark class="h-6 w-6" />
                            </button>
                        </div>

                        <form wire:submit="save" class="space-y-5" x-data="{ eventType: $wire.entangle('type') }">
                            {{-- Title --}}
                            <div>
                                <label for="title" class="block text-sm font-medium text-slate-700 mb-2">
                                    Event Title <span class="text-red-500">*</span>
                                </label>
                                <input
                                    wire:model="title"
                                    type="text"
                                    id="title"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                                    placeholder="e.g., Site Inspection Meeting"
                                />
                                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Attendees (Multiple Select) --}}
                            <x-forms.multi-select
                                wire:model="attendeeIds"
                                :options="$this->availableContacts->pluck('name', 'id')->toArray()"
                                :selected="$attendeeIds"
                                label="Invite Attendees"
                                placeholder="Select attendees..."
                                :required="true"
                                :error="$errors->first('attendeeIds')"
                            />

                            {{-- Event Type --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Event Type <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="relative flex items-center justify-center p-4 rounded-lg border-2 cursor-pointer transition-all {{ $type === 'virtual' ? 'border-teal-500 bg-teal-50' : 'border-slate-200 hover:border-slate-300' }}">
                                        <input type="radio" wire:model.live="type" value="virtual" class="sr-only">
                                        <div class="text-center">
                                            <x-heroicon-o-video-camera class="h-6 w-6 mx-auto mb-1 {{ $type === 'virtual' ? 'text-teal-600' : 'text-slate-400' }}" />
                                            <span class="text-sm font-medium {{ $type === 'virtual' ? 'text-teal-700' : 'text-slate-700' }}">Virtual</span>
                                            <p class="text-xs text-slate-500 mt-0.5">Jitsi Meeting Link</p>
                                        </div>
                                    </label>
                                    <label class="relative flex items-center justify-center p-4 rounded-lg border-2 cursor-pointer transition-all {{ $type === 'physical' ? 'border-teal-500 bg-teal-50' : 'border-slate-200 hover:border-slate-300' }}">
                                        <input type="radio" wire:model.live="type" value="physical" class="sr-only">
                                        <div class="text-center">
                                            <x-heroicon-o-map-pin class="h-6 w-6 mx-auto mb-1 {{ $type === 'physical' ? 'text-teal-600' : 'text-slate-400' }}" />
                                            <span class="text-sm font-medium {{ $type === 'physical' ? 'text-teal-700' : 'text-slate-700' }}">Physical</span>
                                            <p class="text-xs text-slate-500 mt-0.5">In-person Location</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Location (only for physical) --}}
                            <div
                                x-show="eventType === 'physical'"
                                x-collapse
                                x-cloak
                            >
                                <div>
                                    <label for="location" class="block text-sm font-medium text-slate-700 mb-2">
                                        Location <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        wire:model="location"
                                        type="text"
                                        id="location"
                                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                                        placeholder="e.g., 123 Main Street, Lagos"
                                    />
                                    @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Date and Time --}}
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label for="eventDate" class="block text-sm font-medium text-slate-700 mb-2">
                                        Date <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        wire:model="eventDate"
                                        type="date"
                                        id="eventDate"
                                        min="{{ now()->format('Y-m-d') }}"
                                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                                    />
                                    @error('eventDate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="eventTime" class="block text-sm font-medium text-slate-700 mb-2">
                                        Start Time <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        wire:model="eventTime"
                                        type="time"
                                        id="eventTime"
                                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                                    />
                                    @error('eventTime') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="endTime" class="block text-sm font-medium text-slate-700 mb-2">
                                        End Time
                                    </label>
                                    <input
                                        wire:model="endTime"
                                        type="time"
                                        id="endTime"
                                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                                    />
                                    @error('endTime') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Description --}}
                            <div>
                                <label for="description" class="block text-sm font-medium text-slate-700 mb-2">
                                    Description
                                </label>
                                <textarea
                                    wire:model="description"
                                    id="description"
                                    rows="3"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 resize-none"
                                    placeholder="Add event details..."
                                ></textarea>
                                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Info Note --}}
                            <div
                                x-show="eventType === 'virtual' && !{{ $isEditing ? 'true' : 'false' }}"
                                x-collapse
                                x-cloak
                            >
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-teal-50 border border-teal-100">
                                    <x-heroicon-o-information-circle class="h-5 w-5 text-teal-600 flex-shrink-0 mt-0.5" />
                                    <p class="text-sm text-teal-700">
                                        A Jitsi meeting link will be automatically generated and included in the invitation email.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 pt-4">
                                <button
                                    type="submit"
                                    class="flex-1 inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2.5 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all"
                                >
                                    <x-heroicon-o-calendar class="h-4 w-4" />
                                    {{ $isEditing ? 'Update Event' : 'Create & Send Invitations' }}
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
                    </div>
                </div>
            </div>
        </div>
    </template>

    <x-ui.confirm-modal />
</div>

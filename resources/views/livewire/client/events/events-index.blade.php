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

    {{-- Empty contacts alert --}}
    @if($this->availableContacts->isEmpty())
        <div class="p-4 bg-amber-50 border-b border-amber-100 flex items-start gap-3">
            <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-amber-600 mt-0.5 flex-shrink-0" />
            <div class="flex-1">
                <h3 class="text-sm font-medium text-amber-800">No contacts available</h3>
                <p class="text-sm text-amber-700 mt-1">
                    You need to add contacts to your directory before you can invite people to events.
                    <a href="{{ route('app.contacts') }}" class="font-medium underline hover:text-amber-900">Add your first contact &rarr;</a>
                </p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        {{-- Search and Filters --}}
        {{-- View Toggles and Toolbar --}}
        <div class="px-4 py-3 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            {{-- Tabs --}}
            <div class="flex p-1 bg-slate-100 rounded-lg self-start md:self-auto">
                <button
                    wire:click="switchView('list')"
                    class="px-4 py-1.5 text-sm font-medium rounded-md transition-all {{ $view === 'list' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}"
                >
                    List
                </button>
                <button
                    wire:click="switchView('calendar')"
                    class="px-4 py-1.5 text-sm font-medium rounded-md transition-all {{ $view === 'calendar' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}"
                >
                    Calendar
                </button>
            </div>

            {{-- List View Filters (only show in list view) --}}
            @if($view === 'list')
                <div class="flex flex-col md:flex-row gap-3 flex-1 md:justify-end">
                    <div class="relative flex-1 md:max-w-xs">
                        <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                        <input
                            wire:model.live.debounce.300ms="search"
                            type="text"
                            placeholder="Search events..."
                            class="w-full pl-9 pr-4 py-1.5 rounded-lg border border-slate-300 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                        />
                    </div>
                    <div class="w-full md:w-40">
                        <x-forms.searchable-select
                            wire:model.live="filter"
                            :options="['upcoming' => 'Upcoming', 'past' => 'Past', 'all' => 'All Events']"
                            :selected="$filter"
                            placeholder="Filter..."
                        />
                    </div>
                </div>
            @endif
        </div>

        @if($view === 'list')
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
        @endif

        @if($view === 'calendar')
            <div class="p-6">
                <!-- Calendar Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-slate-900">
                        {{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->format('F Y') }}
                    </h2>
                    <div class="flex items-center rounded-lg border border-slate-300 overflow-hidden">
                        <button wire:click="prevMonth" class="p-2 hover:bg-slate-50 border-r border-slate-300">
                            <x-heroicon-o-chevron-left class="h-5 w-5 text-slate-600" />
                        </button>
                        <button wire:click="nextMonth" class="p-2 hover:bg-slate-50">
                            <x-heroicon-o-chevron-right class="h-5 w-5 text-slate-600" />
                        </button>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="border border-slate-200 rounded-lg overflow-hidden">
                    <!-- Days Header -->
                    <div class="grid grid-cols-7 bg-slate-50 border-b border-slate-200">
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="py-2 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                {{ $day }}
                            </div>
                        @endforeach
                    </div>

                    <!-- Days Grid -->
                    <div class="grid grid-cols-7 bg-slate-200 gap-px">
                        @php
                            $startOfCalendar = \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->startOfMonth()->startOfWeek();
                            $endOfCalendar = \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->endOfMonth()->endOfWeek();
                        @endphp

                        @for($date = $startOfCalendar->copy(); $date->lte($endOfCalendar); $date->addDay())
                            @php
                                $isCurrentMonth = $date->month === $currentMonth;
                                $isToday = $date->isToday();
                                $dayEvents = $this->calendarEvents->filter(function($event) use ($date) {
                                    return $event->starts_at->isSameDay($date);
                                });
                            @endphp
                            <div class="min-h-[120px] bg-white p-2 {{ !$isCurrentMonth ? 'bg-slate-50/50' : '' }}">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="text-sm font-medium {{ $isToday ? 'bg-teal-600 text-white w-6 h-6 rounded-full flex items-center justify-center' : ($isCurrentMonth ? 'text-slate-900' : 'text-slate-400') }}">
                                        {{ $date->day }}
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    @foreach($dayEvents as $event)
                                        <button
                                            wire:click="viewEvent({{ $event->id }})"
                                            class="w-full text-left px-2 py-1 rounded text-xs font-medium truncate mb-1
                                                {{ $event->type === 'virtual' ? 'bg-indigo-50 text-indigo-700 border-l-2 border-indigo-500' : 'bg-teal-50 text-teal-700 border-l-2 border-teal-500' }}"
                                        >
                                            {{ $event->starts_at->format('H:i') }} {{ $event->title }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    {{-- Create/Edit Modal --}}
    <x-ui.modal show="showModal" maxWidth="2xl" title="{{ $isEditing ? 'Edit Event' : 'Create New Event' }}">
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
    </x-ui.modal>

    {{-- View Event Modal --}}
    <x-ui.modal show="showViewModal" title="Event Details" maxWidth="lg">
        @if($viewingEvent)
            <div class="space-y-4">
                {{-- Header --}}
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $viewingEvent->type === 'virtual' ? 'bg-indigo-100 text-indigo-800' : 'bg-teal-100 text-teal-800' }}">
                            {{ ucfirst($viewingEvent->type) }}
                        </span>
                        <h3 class="text-lg font-bold text-slate-900">{{ $viewingEvent->title }}</h3>
                    </div>
                    <div class="text-sm text-slate-500 font-medium">
                        {{ $viewingEvent->formatted_date }} • {{ $viewingEvent->formatted_time }}
                    </div>
                </div>

                {{-- Location / Link --}}
                @if($viewingEvent->type === 'virtual' && $viewingEvent->meeting_link)
                    <div class="p-3 bg-indigo-50 rounded-lg flex items-start gap-3">
                        <x-heroicon-o-video-camera class="h-5 w-5 text-indigo-600 mt-0.5" />
                        <div class="overflow-hidden">
                            <p class="text-xs font-semibold text-indigo-900 uppercase tracking-wide">Join Meeting</p>
                            <a href="{{ $viewingEvent->meeting_link }}" target="_blank" class="text-sm text-indigo-700 underline truncate block">
                                {{ $viewingEvent->meeting_link }}
                            </a>
                        </div>
                    </div>
                @elseif($viewingEvent->type === 'physical' && $viewingEvent->location)
                    <div class="p-3 bg-slate-50 rounded-lg flex items-start gap-3">
                        <x-heroicon-o-map-pin class="h-5 w-5 text-slate-500 mt-0.5" />
                        <div>
                            <p class="text-xs font-semibold text-slate-900 uppercase tracking-wide">Location</p>
                            <p class="text-sm text-slate-700">{{ $viewingEvent->location }}</p>
                        </div>
                    </div>
                @endif

                {{-- Description --}}
                @if($viewingEvent->description)
                    <div>
                        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Description</h4>
                        <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $viewingEvent->description }}</p>
                    </div>
                @endif

                {{-- Attendees --}}
                <div>
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">
                        Attendees ({{ $viewingEvent->attendees->count() }})
                    </h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($viewingEvent->attendees as $attendee)
                            <div class="flex items-center gap-1.5 bg-slate-100 rounded-full pl-1.5 pr-3 py-0.5">
                                <div class="h-5 w-5 rounded-full bg-slate-300 flex items-center justify-center text-[10px] font-bold text-slate-600">
                                    {{ substr($attendee->firstname, 0, 1) }}{{ substr($attendee->lastname, 0, 1) }}
                                </div>
                                <span class="text-xs font-medium text-slate-700">{{ $attendee->full_name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-4 border-t border-slate-200 mt-6">
                    <button
                        wire:click="edit({{ $viewingEvent->id }})"
                        class="flex-1 inline-flex justify-center items-center gap-2 rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 transition-colors"
                    >
                        <x-heroicon-o-pencil-square class="h-4 w-4" />
                        Edit Event
                    </button>
                    <button
                        @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                            detail: {
                                title: 'Delete Event',
                                message: 'Are you sure you want to delete this event?',
                                confirmText: 'Delete',
                                cancelText: 'Cancel',
                                variant: 'danger',
                                action: () => $wire.deleteEvent({{ $viewingEvent->id }})
                            }
                        }))"
                        class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                    >
                        Delete
                    </button>
                </div>
            </div>
        @endif
    </x-ui.modal>

    <x-ui.confirm-modal />
</div>

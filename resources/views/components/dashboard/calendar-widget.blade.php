@props(['events', 'eventDates'])

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm flex flex-col overflow-hidden">
    <!-- Calendar Header -->
    <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-teal-500 to-emerald-500">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-white">{{ now()->format('F') }}</h3>
                <p class="text-teal-100 text-sm">{{ now()->format('Y') }}</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold text-white">{{ now()->format('d') }}</p>
                <p class="text-teal-100 text-xs uppercase">{{ now()->format('l') }}</p>
            </div>
        </div>
    </div>

    <!-- Mini Calendar Grid -->
    <div class="p-4">
        <!-- Days of week header -->
        <div class="grid grid-cols-7 gap-1 mb-2">
            @foreach(['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $day)
                <div class="text-center text-[10px] font-semibold text-slate-400 uppercase">{{ $day }}</div>
            @endforeach
        </div>

        <!-- Calendar days -->
        <div class="grid grid-cols-7 gap-1">
            @php
                $startOfMonth = now()->startOfMonth();
                $endOfMonth = now()->endOfMonth();
                $startDayOfWeek = $startOfMonth->dayOfWeek;
                $daysInMonth = $startOfMonth->daysInMonth;
                $today = now()->format('Y-m-d');

                // Previous month days to show
                $prevMonthDays = $startDayOfWeek;
                $prevMonth = now()->subMonth();
                $prevMonthLastDay = $prevMonth->daysInMonth;
            @endphp

            {{-- Previous month days (grayed out) --}}
            @for($i = $prevMonthDays - 1; $i >= 0; $i--)
                <div class="h-7 w-7 mx-auto flex items-center justify-center text-xs text-slate-300">
                    {{ $prevMonthLastDay - $i }}
                </div>
            @endfor

            {{-- Current month days --}}
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $date = $startOfMonth->copy()->addDays($day - 1)->format('Y-m-d');
                    $isToday = $date === $today;
                    $hasEvent = in_array($date, $eventDates ?? []);
                    $isPast = $date < $today;
                @endphp
                <div class="relative h-7 w-7 mx-auto flex items-center justify-center text-xs rounded-full transition-all cursor-default
                    {{ $isToday ? 'bg-teal-500 text-white font-bold shadow-md shadow-teal-500/30' : '' }}
                    {{ !$isToday && $hasEvent ? 'bg-teal-50 text-teal-700 font-medium' : '' }}
                    {{ !$isToday && !$hasEvent && $isPast ? 'text-slate-300' : '' }}
                    {{ !$isToday && !$hasEvent && !$isPast ? 'text-slate-600 hover:bg-slate-50' : '' }}
                ">
                    {{ $day }}
                    @if($hasEvent && !$isToday)
                        <span class="absolute -bottom-0.5 left-1/2 -translate-x-1/2 h-1 w-1 rounded-full bg-teal-500"></span>
                    @endif
                </div>
            @endfor

            {{-- Next month days (grayed out) --}}
            @php
                $totalCells = $prevMonthDays + $daysInMonth;
                $remainingCells = 42 - $totalCells; // 6 rows x 7 days
                if ($remainingCells > 7) $remainingCells = $remainingCells - 7; // Only show if needed
            @endphp
            @for($i = 1; $i <= $remainingCells && $totalCells + $i <= 42; $i++)
                <div class="h-7 w-7 mx-auto flex items-center justify-center text-xs text-slate-300">
                    {{ $i }}
                </div>
            @endfor
        </div>
    </div>

    <!-- Upcoming Events List -->
    <div class="flex-1 border-t border-slate-100">
        <div class="px-4 py-3 bg-slate-50/50">
            <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider flex items-center gap-2">
                <x-heroicon-o-calendar-days class="h-3.5 w-3.5" />
                Upcoming Events
            </h4>
        </div>

        <div class="max-h-[200px] overflow-y-auto">
            @forelse($events as $event)
                <a href="{{ route('app.events.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-b-0">
                    <!-- Date box -->
                    <div class="flex-shrink-0 w-11 h-11 rounded-lg bg-gradient-to-br {{ $event->isVirtual() ? 'from-indigo-500 to-purple-500' : 'from-teal-500 to-emerald-500' }} flex flex-col items-center justify-center text-white shadow-sm">
                        <span class="text-[10px] font-medium uppercase leading-none">{{ $event->starts_at->format('M') }}</span>
                        <span class="text-base font-bold leading-none">{{ $event->starts_at->format('d') }}</span>
                    </div>

                    <!-- Event details -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900 truncate">{{ $event->title }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs text-slate-500">{{ $event->starts_at->format('g:i A') }}</span>
                            <span class="inline-flex items-center gap-1 text-xs {{ $event->isVirtual() ? 'text-indigo-600' : 'text-teal-600' }}">
                                @if($event->isVirtual())
                                    <x-heroicon-o-video-camera class="h-3 w-3" />
                                    Virtual
                                @else
                                    <x-heroicon-o-map-pin class="h-3 w-3" />
                                    In-person
                                @endif
                            </span>
                        </div>
                    </div>

                    <x-heroicon-o-chevron-right class="h-4 w-4 text-slate-300 flex-shrink-0" />
                </a>
            @empty
                <div class="px-4 py-8 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center">
                        <x-heroicon-o-calendar class="h-6 w-6 text-slate-400" />
                    </div>
                    <p class="text-sm text-slate-500">No upcoming events</p>
                    <a href="{{ route('app.events.index') }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-medium text-teal-600 hover:text-teal-700 mt-2">
                        <x-heroicon-o-plus class="h-3 w-3" />
                        Schedule one
                    </a>
                </div>
            @endforelse
        </div>

        @if($events->count() > 0)
            <div class="px-4 py-2 bg-slate-50/50 border-t border-slate-100">
                <a href="{{ route('app.events.index') }}" wire:navigate class="text-xs font-medium text-teal-600 hover:text-teal-700 flex items-center justify-center gap-1">
                    View all events
                    <x-heroicon-o-arrow-right class="h-3 w-3" />
                </a>
            </div>
        @endif
    </div>
</div>

@props(['events', 'eventDates'])

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm flex flex-col h-full overflow-hidden">
    <!-- Calendar Header -->
    <div class="p-6 border-b border-slate-50 flex justify-between items-center">
        <h3 class="font-semibold text-slate-900">{{ now()->format('F Y') }}</h3>
        <div class="flex space-x-2">
            <button class="p-1 hover:bg-slate-50 rounded-full text-slate-400 hover:text-slate-600 transition">
                <x-heroicon-o-chevron-left class="w-4 h-4" />
            </button>
            <button class="p-1 hover:bg-slate-50 rounded-full text-slate-400 hover:text-slate-600 transition">
                <x-heroicon-o-chevron-right class="w-4 h-4" />
            </button>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="p-6 pb-2">
        <div class="grid grid-cols-7 gap-1 text-center mb-2">
            @foreach(['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'] as $day)
                <span class="text-xs font-medium text-slate-400 uppercase">{{ $day }}</span>
            @endforeach
        </div>
        <div class="grid grid-cols-7 gap-1 text-center text-sm">
            @php
                $startOfMonth = now()->startOfMonth();
                $endOfMonth = now()->endOfMonth();
                $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 (Sun) - 6 (Sat)
                $daysInMonth = $startOfMonth->daysInMonth;
                $today = now()->format('Y-m-d');
            @endphp

            {{-- Empty cells for previous month --}}
            @for($i = 0; $i < $startDayOfWeek; $i++)
                <div class="h-8"></div>
            @endfor

            {{-- Days --}}
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $date = $startOfMonth->copy()->addDays($day - 1)->format('Y-m-d');
                    $isToday = $date === $today;
                    $hasEvent = in_array($date, $eventDates);
                @endphp
                <div class="h-8 w-8 mx-auto flex items-center justify-center rounded-full relative group cursor-pointer hover:bg-slate-50 transition {{ $isToday ? 'bg-slate-900 text-white hover:bg-slate-800' : 'text-slate-700' }}">
                    <span>{{ $day }}</span>
                    @if($hasEvent && !$isToday)
                        <span class="absolute bottom-1 h-1 w-1 rounded-full bg-indigo-500"></span>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    <!-- Upcoming Events List -->
    <div class="px-6 pb-6 pt-2 flex-1 overflow-y-auto">
        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Upcoming Events</h4>
        
        <div class="space-y-4">
            @forelse($events as $event)
                <div class="flex items-start group">
                    <div class="flex-shrink-0 w-12 text-center">
                        <span class="block text-xs text-slate-500 font-medium">{{ $event->start_time->format('M') }}</span>
                        <span class="block text-lg font-bold text-slate-900">{{ $event->start_time->format('d') }}</span>
                    </div>
                    <div class="ml-3 pl-3 border-l-2 border-transparent group-hover:border-indigo-500 transition-colors duration-200">
                        <p class="text-sm font-semibold text-slate-900 truncate">{{ $event->title }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $event->start_time->format('h:i A') }} • {{ $event->isVirtual() ? 'Virtual' : 'Physical' }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <p class="text-sm text-slate-400">No upcoming events</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

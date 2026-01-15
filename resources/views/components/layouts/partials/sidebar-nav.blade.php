<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <div class="text-xs font-semibold leading-6 text-slate-500 uppercase tracking-widest mb-4 px-2">Overview</div>
            <ul role="list" class="-mx-2 space-y-2">
                <li>
                    <a href="{{ route('app.dashboard') }}" wire:navigate class="group flex gap-x-3 rounded-xl p-2.5 text-sm leading-6 font-semibold transition-all duration-300 {{ request()->routeIs('app.dashboard') ? 'bg-gradient-to-r from-teal-500/10 to-transparent text-teal-400 border-l-4 border-teal-500 shadow-[0_0_20px_rgba(45,212,191,0.1)]' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent' }}">
                        <x-heroicon-o-presentation-chart-line class="h-6 w-6 shrink-0 {{ request()->routeIs('app.dashboard') ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400 transition-colors' }}" />
                        Dashboard
                    </a>
                </li>
            </ul>
        </li>

        @canany(['view facilities', 'view work orders'])
        <li>
            <div class="text-xs font-semibold leading-6 text-slate-500 uppercase tracking-widest mb-4 px-2">Management</div>
            <ul role="list" class="-mx-2 space-y-2">
                @can('view facilities')
                <li>
                    <a href="{{ route('app.facilities') }}" wire:navigate class="group flex gap-x-3 rounded-xl p-2.5 text-sm leading-6 font-semibold transition-all duration-300 {{ request()->routeIs('app.facilities') ? 'bg-gradient-to-r from-teal-500/10 to-transparent text-teal-400 border-l-4 border-teal-500 shadow-[0_0_20px_rgba(45,212,191,0.1)]' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent' }}">
                        <x-heroicon-o-building-office-2 class="h-6 w-6 shrink-0 {{ request()->routeIs('app.facilities') ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400 transition-colors' }}" />
                        Facilities
                    </a>
                </li>
                @endcan
                @can('viewAny', \App\Models\WorkOrder::class)
                 <li>
                    <a href="{{ route('app.work-orders.index') }}" wire:navigate class="group flex gap-x-3 rounded-xl p-2.5 text-sm leading-6 font-semibold transition-all duration-300 {{ request()->routeIs('app.work-orders') ? 'bg-gradient-to-r from-teal-500/10 to-transparent text-teal-400 border-l-4 border-teal-500 shadow-[0_0_20px_rgba(45,212,191,0.1)]' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent' }}">
                        <x-heroicon-o-clipboard-document-list class="h-6 w-6 shrink-0 {{ request()->routeIs('app.work-orders') ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400 transition-colors' }}" />
                        Work Orders
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcanany

        @canany(['view contacts', 'view sla policy', 'view users', 'view roles'])
        <li>
            <div class="text-xs font-semibold leading-6 text-slate-500 uppercase tracking-widest mb-4 px-2">Compliance & People</div>
            <ul role="list" class="-mx-2 space-y-2">
                {{-- Contacts Dropdown --}}
                @can('view contacts')
                <li x-data="{ open: {{ request()->routeIs('app.contacts*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="group flex w-full items-center justify-between gap-x-3 rounded-xl p-2.5 text-sm leading-6 font-semibold transition-all duration-300 {{ request()->routeIs('app.contacts*') ? 'bg-gradient-to-r from-teal-500/10 to-transparent text-teal-400 border-l-4 border-teal-500' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent' }}">
                        <div class="flex items-center gap-x-3">
                            <x-heroicon-o-user-group class="h-6 w-6 shrink-0 {{ request()->routeIs('app.contacts*') ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400 transition-colors' }}" />
                            <span>Contacts</span>
                        </div>
                        <x-heroicon-o-chevron-right class="h-5 w-5 shrink-0 transition-transform duration-200" ::class="open ? 'rotate-90' : ''" />
                    </button>
                    
                    <ul x-show="open" x-collapse class="mt-2 space-y-1 pl-11">
                        <li>
                            <a href="{{ route('app.contacts') }}" wire:navigate class="group flex gap-x-3 rounded-lg p-2 text-sm leading-6 font-medium transition-all {{ request()->routeIs('app.contacts') && !request()->routeIs('app.contacts.types') && !request()->routeIs('app.contacts.groups') ? 'text-teal-400' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                                All Contacts
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('app.contacts.types') }}" wire:navigate class="group flex gap-x-3 rounded-lg p-2 text-sm leading-6 font-medium transition-all {{ request()->routeIs('app.contacts.types') ? 'text-teal-400' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                                Contact Types
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('app.contacts.groups') }}" wire:navigate class="group flex gap-x-3 rounded-lg p-2 text-sm leading-6 font-medium transition-all {{ request()->routeIs('app.contacts.groups') ? 'text-teal-400' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                                Contact Groups
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan
                
                 @can('view sla policy')
                 <li>
                    <a href="{{ route('app.sla-policy') }}" wire:navigate class="group flex gap-x-3 rounded-xl p-2.5 text-sm leading-6 font-semibold transition-all duration-300 {{ request()->routeIs('app.sla-policy') ? 'bg-gradient-to-r from-teal-500/10 to-transparent text-teal-400 border-l-4 border-teal-500 shadow-[0_0_20px_rgba(45,212,191,0.1)]' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent' }}">
                        <x-heroicon-o-clock class="h-6 w-6 shrink-0 {{ request()->routeIs('app.sla-policy') ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400 transition-colors' }}" />
                        SLA Policy
                    </a>
                </li>
                @endcan
                @can('view users')
                <li>
                    <a href="{{ route('app.users') }}" wire:navigate class="group flex gap-x-3 rounded-xl p-2.5 text-sm leading-6 font-semibold transition-all duration-300 {{ request()->routeIs('app.users') ? 'bg-gradient-to-r from-teal-500/10 to-transparent text-teal-400 border-l-4 border-teal-500 shadow-[0_0_20px_rgba(45,212,191,0.1)]' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent' }}">
                        <x-heroicon-o-users class="h-6 w-6 shrink-0 {{ request()->routeIs('app.users') ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400 transition-colors' }}" />
                        Users
                    </a>
                </li>
                @endcan

                @can('view roles')
                <li>
                    <a href="{{ route('app.roles') }}" wire:navigate class="group flex gap-x-3 rounded-xl p-2.5 text-sm leading-6 font-semibold transition-all duration-300 {{ request()->routeIs('app.roles') ? 'bg-gradient-to-r from-teal-500/10 to-transparent text-teal-400 border-l-4 border-teal-500 shadow-[0_0_20px_rgba(45,212,191,0.1)]' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent' }}">
                        <x-heroicon-o-shield-check class="h-6 w-6 shrink-0 {{ request()->routeIs('app.roles') ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400 transition-colors' }}" />
                        Roles
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcanany
    </ul>
</nav>

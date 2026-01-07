<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <div class="text-xs font-semibold leading-6 text-slate-500 uppercase tracking-widest mb-4 px-2">Overview</div>
            <ul role="list" class="-mx-2 space-y-2">
                <li>
                    <a href="{{ route('app.dashboard') }}" wire:navigate class="group flex gap-x-3 rounded-xl p-2.5 text-sm leading-6 font-semibold transition-all duration-300 {{ request()->routeIs('app.dashboard') ? 'bg-gradient-to-r from-teal-500/10 to-transparent text-teal-400 border-l-4 border-teal-500 shadow-[0_0_20px_rgba(45,212,191,0.1)]' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('app.dashboard') ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                        </svg>
                        Dashboard
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <div class="text-xs font-semibold leading-6 text-slate-500 uppercase tracking-widest mb-4 px-2">Management</div>
            <ul role="list" class="-mx-2 space-y-2">
                @can('view facilities')
                <li>
                    <a href="{{ route('app.facilities') }}" wire:navigate class="group flex gap-x-3 rounded-xl p-2.5 text-sm leading-6 font-semibold transition-all duration-300 {{ request()->routeIs('app.facilities') ? 'bg-gradient-to-r from-teal-500/10 to-transparent text-teal-400 border-l-4 border-teal-500 shadow-[0_0_20px_rgba(45,212,191,0.1)]' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('app.facilities') ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                        </svg>
                        Facilities
                    </a>
                </li>
                @endcan
                @can('view work orders')
                 <li>
                    <a href="{{ route('app.work-orders') }}" wire:navigate class="group flex gap-x-3 rounded-xl p-2.5 text-sm leading-6 font-semibold transition-all duration-300 {{ request()->routeIs('app.work-orders') ? 'bg-gradient-to-r from-teal-500/10 to-transparent text-teal-400 border-l-4 border-teal-500 shadow-[0_0_20px_rgba(45,212,191,0.1)]' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('app.work-orders') ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                        </svg>
                        Work Orders
                    </a>
                </li>
                @endcan
            </ul>
        </li>

        <li>
            <div class="text-xs font-semibold leading-6 text-slate-500 uppercase tracking-widest mb-4 px-2">Compliance & People</div>
            <ul role="list" class="-mx-2 space-y-2">
                 @can('view sla policy')
                 <li>
                    <a href="{{ route('app.sla-policy') }}" wire:navigate class="group flex gap-x-3 rounded-xl p-2.5 text-sm leading-6 font-semibold transition-all duration-300 {{ request()->routeIs('app.sla-policy') ? 'bg-gradient-to-r from-teal-500/10 to-transparent text-teal-400 border-l-4 border-teal-500 shadow-[0_0_20px_rgba(45,212,191,0.1)]' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('app.sla-policy') ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        SLA Policy
                    </a>
                </li>
                @endcan
                @can('view vendors')
                 <li>
                    <a href="{{ route('app.vendors') }}" wire:navigate class="group flex gap-x-3 rounded-xl p-2.5 text-sm leading-6 font-semibold transition-all duration-300 {{ request()->routeIs('app.vendors') ? 'bg-gradient-to-r from-teal-500/10 to-transparent text-teal-400 border-l-4 border-teal-500 shadow-[0_0_20px_rgba(45,212,191,0.1)]' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('app.vendors') ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                        </svg>
                        Vendors
                    </a>
                </li>
                @endcan
                @can('view users')
                <li>
                    <a href="{{ route('app.users') }}" wire:navigate class="group flex gap-x-3 rounded-xl p-2.5 text-sm leading-6 font-semibold transition-all duration-300 {{ request()->routeIs('app.users') ? 'bg-gradient-to-r from-teal-500/10 to-transparent text-teal-400 border-l-4 border-teal-500 shadow-[0_0_20px_rgba(45,212,191,0.1)]' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('app.users') ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        Users
                    </a>
                </li>
                @endcan
                @can('view roles')
                <li>
                    <a href="{{ route('app.roles') }}" wire:navigate class="group flex gap-x-3 rounded-xl p-2.5 text-sm leading-6 font-semibold transition-all duration-300 {{ request()->routeIs('app.roles') ? 'bg-gradient-to-r from-teal-500/10 to-transparent text-teal-400 border-l-4 border-teal-500 shadow-[0_0_20px_rgba(45,212,191,0.1)]' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent' }}">
                        <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('app.roles') ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400 transition-colors' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                        Roles
                    </a>
                </li>
                @endcan
            </ul>
        </li>
    </ul>
</nav>

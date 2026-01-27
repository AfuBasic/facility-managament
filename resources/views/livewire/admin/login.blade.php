<div class="w-full max-w-md p-8 relative z-10">
    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl shadow-2xl overflow-hidden">

        <div class="p-8">
            <h2 class="text-2xl font-bold text-center text-white mb-2 tracking-tight">
                Admin Login
            </h2>
            <p class="text-center text-slate-400 mb-8 text-sm">Sign in to access the admin panel</p>

            <form wire:submit="login" class="space-y-6">
                <!-- Email -->
                <div class="group">
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-1 ml-1">Email Address</label>
                    <input wire:model="email" type="email" id="email"
                           class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent focus:bg-white/10 transition-all duration-300"
                           placeholder="admin@example.com">
                    @error('email')
                        <div class="mt-2 text-sm text-red-400 flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="group">
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-1 ml-1">Password</label>
                    <input wire:model="password" type="password" id="password"
                           class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent focus:bg-white/10 transition-all duration-300"
                           placeholder="••••••••">
                    @error('password')
                        <div class="mt-2 text-sm text-red-400 flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input wire:model="remember" id="remember-me" type="checkbox" class="h-4 w-4 text-slate-500 focus:ring-slate-400 border-gray-300 rounded bg-white/10 border-white/20">
                    <label for="remember-me" class="ml-2 block text-sm text-slate-300">
                        Remember me
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-slate-900 bg-slate-300 hover:bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-slate-400 transition-all duration-300 disabled:opacity-75 disabled:cursor-wait">
                    <span wire:loading.remove>Sign In</span>
                    <span wire:loading class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-slate-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>

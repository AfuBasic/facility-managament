<div class="w-full max-w-lg p-8 relative z-10">
    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
        
        <div class="p-8">
            <h2 class="text-3xl font-bold text-center text-white mb-2 tracking-tight drop-shadow-md">
                Welcome Back
            </h2>
            <p class="text-center text-teal-200 mb-8 text-sm">Sign in to your account to continue</p>

            <form wire:submit="login" class="space-y-6">
                <!-- Email -->
                <div class="group">
                    <label for="email" class="block text-sm font-medium text-teal-200 mb-1 ml-1 group-focus-within:text-teal-400 transition-colors">Email Address</label>
                    <input wire:model="email" type="email" id="email" 
                           class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent focus:bg-white/10 transition-all duration-300 shadow-inner"
                           placeholder="you@example.com">
                    @error('email') 
                        <div class="mt-2 text-sm text-red-500 flex items-center animate-pulse">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $message }}
                        </div> 
                    @enderror
                </div>

                <!-- Password -->
                <div class="group">
                    <div class="flex justify-between items-center mb-1 ml-1">
                        <label for="password" class="block text-sm font-medium text-teal-200 group-focus-within:text-teal-400 transition-colors">Password</label>
                        <a href="#" class="text-xs text-teal-400 hover:text-teal-300 transition-colors">Forgot password?</a>
                    </div>
                    <input wire:model="password" type="password" id="password" 
                           class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent focus:bg-white/10 transition-all duration-300 shadow-inner"
                           placeholder="••••••••">
                    @error('password') 
                        <div class="mt-2 text-sm text-red-500 flex items-center animate-pulse">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $message }}
                        </div> 
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input wire:model="remember" id="remember-me" type="checkbox" class="h-4 w-4 text-teal-400 focus:ring-teal-400 border-gray-300 rounded bg-white/10 border-white/20">
                    <label for="remember-me" class="ml-2 block text-sm text-teal-200">
                        Remember me
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-teal-900 bg-teal-400 hover:bg-teal-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-teal-400 transition-all duration-300 transform hover:scale-[1.02] shadow-[0_0_20px_rgba(45,212,191,0.3)] hover:shadow-[0_0_30px_rgba(45,212,191,0.5)] disabled:opacity-75 disabled:cursor-wait">
                    <span wire:loading.remove>Sign In</span>
                    <span wire:loading class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-teal-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Signing in...
                    </span>
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-teal-200/80">
                Don't have an account? 
                <a href="{{ route('signup') }}" wire:navigate class="font-medium text-teal-400 hover:text-teal-300 transition-colors">Sign up</a>
            </div>
        </div>
    </div>
</div>

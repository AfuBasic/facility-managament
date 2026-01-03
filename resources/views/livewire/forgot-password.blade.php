<div class="w-full max-w-lg p-8 relative z-10">
    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
        
        <div class="p-8">
            <h2 class="text-3xl font-bold text-center text-white mb-2 tracking-tight drop-shadow-md">
                Forgot Password?
            </h2>
            <p class="text-center text-teal-200 mb-8 text-sm">Enter your email and we'll send you a reset link</p>

            @if($status)
                <div class="mb-6 bg-teal-400/20 border border-teal-400/50 rounded-xl p-4 flex items-center">
                    <svg class="h-5 w-5 text-teal-300 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-teal-100 font-medium">{{ $status }}</span>
                </div>
            @endif

            <form wire:submit="submit" class="space-y-6">
                
                <div class="group">
                    <label for="email" class="block text-sm font-medium text-teal-200 mb-1 ml-1 group-focus-within:text-teal-400 transition-colors">Email Address</label>
                    <input wire:model="email" type="email" id="email" 
                           class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent focus:bg-white/10 transition-all duration-300 shadow-inner"
                           placeholder="you@company.com">
                    @error('email') 
                        <div class="mt-2 text-sm text-red-500 flex items-center animate-pulse">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $message }}
                        </div> 
                    @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-teal-900 bg-teal-400 hover:bg-teal-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-teal-400 transition-all duration-300 transform hover:scale-[1.02] shadow-[0_0_20px_rgba(45,212,191,0.3)] hover:shadow-[0_0_30px_rgba(45,212,191,0.5)] disabled:opacity-75 disabled:cursor-wait">
                    <span wire:loading.remove>Send Reset Link</span>
                    <span wire:loading class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-teal-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                    </span>
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-teal-200/80">
                <a href="{{ route('login') }}" class="font-medium text-teal-400 hover:text-teal-300 transition-colors">Back to Login</a>
            </div>
        </div>
    </div>
</div>
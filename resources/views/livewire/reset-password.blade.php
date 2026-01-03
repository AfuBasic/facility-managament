<div class="w-full max-w-lg p-8 relative z-10">
    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
        
        <div class="p-8">
            <h2 class="text-3xl font-bold text-center text-white mb-2 tracking-tight drop-shadow-md">
                Reset Password
            </h2>
            <p class="text-center text-teal-200 mb-8 text-sm">Create a new secure password</p>

            <form wire:submit="resetPassword" class="space-y-6">
                <!-- Email (Read-only) -->
                <input type="hidden" wire:model="token">
                
                <div class="group">
                    <label for="email" class="block text-sm font-medium text-teal-200 mb-1 ml-1 cursor-not-allowed opacity-75">Email Address</label>
                    <input wire:model="email" type="email" id="email" readonly
                           class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white/50 cursor-not-allowed focus:outline-none shadow-inner">
                </div>

                <!-- Password -->
                <div class="group">
                    <label for="password" class="block text-sm font-medium text-teal-200 mb-1 ml-1 group-focus-within:text-teal-400 transition-colors">New Password</label>
                    <input wire:model="password" type="password" id="password" 
                           class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent focus:bg-white/10 transition-all duration-300 shadow-inner"
                           placeholder="••••••••">
                </div>

                <!-- Confirm Password -->
                <div class="group">
                    <label for="password_confirmation" class="block text-sm font-medium text-teal-200 mb-1 ml-1 group-focus-within:text-teal-400 transition-colors">Confirm Password</label>
                    <input wire:model="password_confirmation" type="password" id="password_confirmation" 
                           class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent focus:bg-white/10 transition-all duration-300 shadow-inner"
                           placeholder="••••••••">
                </div>

                <!-- Submit Button -->
                <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-teal-900 bg-teal-400 hover:bg-teal-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-teal-400 transition-all duration-300 transform hover:scale-[1.02] shadow-[0_0_20px_rgba(45,212,191,0.3)] hover:shadow-[0_0_30px_rgba(45,212,191,0.5)] disabled:opacity-75 disabled:cursor-wait">
                    <span wire:loading.remove>Reset Password</span>
                    <span wire:loading class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-teal-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-teal-200/80">
                <a href="{{ route('login') }}" wire:navigate class="font-medium text-teal-400 hover:text-teal-300 transition-colors">Back to Login</a>
            </div>
        </div>
    </div>
</div>

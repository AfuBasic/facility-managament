<div class="w-full max-w-lg p-8 relative z-10" x-data="{ step: @entangle('step') }">
    
    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
        
        <!-- Header / Progress -->
        <div class="bg-black/20 p-6 border-b border-white/10">
            <div class="flex items-start justify-between relative">
                
                <!-- Step 1 Indicator -->
                <div class="flex flex-col items-center relative z-10 w-20">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg transition-all duration-300 transform "
                            :class="step >= 1 ? 'bg-teal-400 text-teal-900 scale-110 shadow-[0_0_15px_rgba(45,212,191,0.5)] border-teal-400' : 'text-white/50'">
                        1
                    </div>
                    <span class="text-xs mt-2 font-medium tracking-wide uppercase transition-colors duration-300 text-center"
                            :class="step >= 1 ? 'text-teal-400' : 'text-white/50'">Organization</span>
                </div>

                <!-- Connector Line -->
                <div class="flex-1 mt-5 h-0.5 bg-white/10 relative mx-2">
                    <div class="absolute top-0 left-0 h-full bg-teal-400 transition-all duration-500 ease-out"
                            :style="step === 1 ? 'width: 0%' : 'width: 100%'"></div>
                </div>

                <!-- Step 2 Indicator -->
                <div class="flex flex-col items-center relative z-10 w-20">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg transition-all duration-300 transform "
                            :class="step >= 2 ? 'bg-teal-400 text-teal-900 scale-110 shadow-[0_0_15px_rgba(45,212,191,0.5)] border-teal-400' : 'text-white/50'">
                        2
                    </div>
                    <span class="text-xs mt-2 font-medium tracking-wide uppercase transition-colors duration-300 text-center"
                            :class="step >= 2 ? 'text-teal-400' : 'text-white/50'">Account</span>
                </div>

            </div>
        </div>

        <div class="p-8 relative min-h-[500px]">
            
            <!-- Animated Title -->
            <h2 class="text-3xl font-bold text-center text-white mb-8 tracking-tight drop-shadow-md"
                x-text="step === 1 ? 'Setup Organization' : 'Create Account'"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                key="title">
            </h2>

            <!-- Step 1 Content -->
            <div x-show="step === 1"
                    x-transition:enter="transition ease-out duration-500 delay-100"
                    x-transition:enter-start="opacity-0 transform -translate-x-10"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-x-0"
                    x-transition:leave-end="opacity-0 transform -translate-x-10"
                    class="absolute inset-0 p-8 pt-28"> <!-- Absolute to overlap for smooth transition -->
                
                <div class="flex h-full flex-col justify-between space-y-6">
                    <div class="group">
                        <label for="organization_name" class="block text-sm font-medium text-teal-200 mb-1 ml-1 group-focus-within:text-teal-400 transition-colors">Organization Name</label>
                        <input wire:model="organization_name" type="text" id="organization_name" 
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent focus:bg-white/10 transition-all duration-300 shadow-inner"
                                placeholder="Acme Corp">
                        @error('organization_name') 
                            <div class="mt-2 text-sm text-red-500 flex items-center animate-pulse">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $message }}
                            </div> 
                        @enderror
                    </div>

                    <div class="pt-4">
                        <button wire:click="nextStep" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-teal-900 bg-teal-400 hover:bg-teal-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-teal-400 transition-all duration-300 transform hover:scale-[1.02] shadow-[0_0_20px_rgba(45,212,191,0.3)] hover:shadow-[0_0_30px_rgba(45,212,191,0.5)]">
                            <span>Continue</span>
                            <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2 Content -->
            <div x-show="step === 2" style="display: none;"
                    x-transition:enter="transition ease-out duration-500 delay-100"
                    x-transition:enter-start="opacity-0 transform translate-x-10"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-x-0"
                    x-transition:leave-end="opacity-0 transform translate-x-10"
                    class="absolute inset-0 p-8 pt-20">

                <div class="flex flex-col justify-between h-full space-y-5">
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

                    <div class="grid gap-4">
                        <div class="group">
                            <label for="password" class="block text-sm font-medium text-teal-200 mb-1 ml-1 group-focus-within:text-teal-400 transition-colors">Password</label>
                            <input wire:model="password" type="password" id="password" 
                                    class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent focus:bg-white/10 transition-all duration-300 shadow-inner"
                                    placeholder="••••••••">
                        </div>
                        <div class="group">
                            <label for="password_confirmation" class="block text-sm font-medium text-teal-200 mb-1 ml-1 group-focus-within:text-teal-400 transition-colors">Confirm</label>
                            <input wire:model="password_confirmation" type="password" id="password_confirmation" 
                                    class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent focus:bg-white/10 transition-all duration-300 shadow-inner"
                                    placeholder="••••••••">
                        </div>
                    </div>
                    @error('password') 
                        <div class="text-sm text-red-500 flex items-center animate-pulse">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $message }}
                        </div> 
                    @enderror

                    <div class="pt-4 flex space-x-4">
                        <button wire:click="previousStep" class="w-1/3 py-3 px-4 border border-white/20 rounded-xl text-sm font-medium text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-teal-400 transition-all duration-300">
                            Back
                        </button>
                        <button wire:click="submit" wire:loading.attr="disabled" class="w-2/3 flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-teal-900 bg-teal-400 hover:bg-teal-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-teal-400 transition-all duration-300 transform hover:scale-[1.02] shadow-[0_0_20px_rgba(45,212,191,0.3)] hover:shadow-[0_0_30px_rgba(45,212,191,0.5)] disabled:opacity-75 disabled:cursor-wait">
                            <span wire:loading.remove>Create Account</span>
                            <x-loading-spinner />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

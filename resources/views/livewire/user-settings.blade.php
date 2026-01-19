<div class="max-w-3xl mx-auto relative"> <!-- Centralized Container & Relative for Modal -->
    <div class="mb-8 text-center"> <!-- Centered Header -->
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Account Settings</h1>
        <p class="text-slate-500 mt-2">Manage your profile information and security.</p>
    </div>

    <div class="space-y-6">
        
        <!-- Profile Information (Name) -->
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
            <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                <div class="h-8 w-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
                Profile Information
            </h2>

            <form wire:submit="updateInformation" class="space-y-6">
                <!-- Name -->
                <div class="group">
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1 ml-1 group-focus-within:text-teal-600 transition-colors">Full Name</label>
                    <input wire:model="name" type="text" id="name" 
                            class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all duration-300 shadow-sm"
                            placeholder="John Doe">
                    @error('name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" wire:loading.attr="disabled" wire:target="updateInformation"
                        class="px-6 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition-all duration-300 shadow-lg shadow-slate-900/10 hover:shadow-slate-900/20 disabled:opacity-75 disabled:cursor-wait flex items-center">
                        <span wire:loading.remove wire:target="updateInformation">Save Name</span>
                        <span wire:loading wire:target="updateInformation" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Email Address -->
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
            <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                <div class="h-8 w-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                       <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </div>
                Email Address
            </h2>

             <form wire:submit="initiateEmailUpdate" class="space-y-6">
                <!-- Email -->
                <div class="group">
                    <label for="new_email" class="block text-sm font-medium text-slate-700 mb-1 ml-1 group-focus-within:text-teal-600 transition-colors">Email Address</label>
                    <input wire:model="new_email" type="email" id="new_email" 
                            class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all duration-300 shadow-sm"
                            placeholder="john@example.com">
                    @error('new_email') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" wire:loading.attr="disabled" wire:target="initiateEmailUpdate"
                        class="px-6 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition-all duration-300 shadow-lg shadow-slate-900/10 hover:shadow-slate-900/20 disabled:opacity-75 disabled:cursor-wait flex items-center">
                        <span wire:loading.remove wire:target="initiateEmailUpdate">Update Email</span>
                        <span wire:loading wire:target="initiateEmailUpdate" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Update Password -->
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
             <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                <div class="h-8 w-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                Update Password
            </h2>

            <form wire:submit="updatePassword" class="space-y-6">
                
                <div class="group">
                    <label for="current_password" class="block text-sm font-medium text-slate-700 mb-1 ml-1 group-focus-within:text-teal-600 transition-colors">Current Password</label>
                    <input wire:model="current_password" type="password" id="current_password" 
                           class="block w-full max-w-md px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all duration-300 shadow-sm"
                           placeholder="••••••••">
                    @error('current_password') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="group">
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1 ml-1 group-focus-within:text-teal-600 transition-colors">New Password</label>
                        <input wire:model="password" type="password" id="password" 
                               class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all duration-300 shadow-sm"
                               placeholder="••••••••">
                        @error('password') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="group">
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1 ml-1 group-focus-within:text-teal-600 transition-colors">Confirm New Password</label>
                        <input wire:model="password_confirmation" type="password" id="password_confirmation" 
                               class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all duration-300 shadow-sm"
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" wire:loading.attr="disabled" wire:target="updatePassword"
                        class="px-6 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition-all duration-300 shadow-lg shadow-slate-900/10 hover:shadow-slate-900/20 disabled:opacity-75 disabled:cursor-wait flex items-center">
                        <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                         <span wire:loading wire:target="updatePassword" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- OTP Modal -->
    <x-ui.modal show="showOtpModal" title="Verify Email" maxWidth="md">
        <div class="text-center mb-6">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-teal-100 mb-4">
                <svg class="h-8 w-8 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <p class="text-sm text-slate-500">
                We've sent a 6-digit verification code to <br/> <strong class="text-slate-900">{{ $new_email }}</strong>
            </p>
        </div>

        <form wire:submit="verifyOtpAndSave">
            <div class="mb-6">
                <label for="otp_code" class="block text-sm font-medium text-slate-700 mb-2 text-center">Enter Verification Code</label>
                <input wire:model="otp_code" type="text" id="otp_code" maxlength="6"
                        class="block w-full px-4 py-3 text-center text-2xl tracking-[0.5em] font-bold bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all duration-300 shadow-sm"
                        placeholder="000000">
                @error('otp_code') <span class="text-red-500 text-sm mt-2 block text-center">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button type="button" wire:click="cancelEmailUpdate"
                        class="w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" wire:loading.attr="disabled" wire:target="verifyOtpAndSave"
                        class="w-full justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-colors flex items-center">
                    <span wire:loading.remove wire:target="verifyOtpAndSave">Verify</span>
                    <span wire:loading wire:target="verifyOtpAndSave">Verifying...</span>
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>

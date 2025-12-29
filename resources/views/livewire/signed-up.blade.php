<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

        <!-- Icon -->
        <div class="mx-auto mb-6 flex items-center justify-center w-14 h-14 rounded-full bg-emerald-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-emerald-600" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <!-- Heading -->
        <h1 class="text-xl font-semibold text-gray-900 mb-2">
            Account created successfully
        </h1>

        <!-- Message -->
        <p class="text-sm text-gray-600 mb-6 leading-relaxed">
            Your Optima FM account has been created.
            We’ve sent a confirmation email to your inbox.
            Please verify your email address to activate your account.
        </p>

        <!-- Actions -->
        <div class="space-y-3">
            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center w-full rounded-lg bg-emerald-600 px-4 py-2.5
                      text-sm font-medium text-white hover:bg-emerald-700 transition">
                Home
            </a>

            <p class="text-xs text-gray-500">
                Didn’t receive the email? Check your spam folder or
                <button class="text-emerald-600 hover:underline cursor-pointer" wire:click="sendVerificationEmail" wire:loading.remove>resend confirmation</button>.
                <span wire:loading>Sending...</span>
            </p>
        </div>

    </div>
</div>
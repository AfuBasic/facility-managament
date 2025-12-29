<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

        <!-- Icon -->
        <div class="mx-auto mb-6 flex items-center justify-center w-14 h-14 rounded-full bg-emerald-50">
            <svg xmlns="http://www.w3.org/2000/svg"
     class="w-7 h-7 text-amber-600"
     fill="none"
     viewBox="0 0 24 24"
     stroke="currentColor"
     stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round"
          d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
</svg>
        </div>

        <!-- Heading -->
        <h1 class="text-xl font-semibold text-gray-900 mb-2">
            Your email is unverified
        </h1>

        <!-- Message -->
        <p class="text-sm text-gray-600 mb-6 leading-relaxed">
            A verification email has been sent to your email address.
            Please verify your email address to activate your account.
        </p>
        <p class="text-sm text-gray-600 mb-6 leading-relaxed">
            Didn't receive the email? Check your spam folder or you can just click the button below to resend the email.
        </p>
        <!-- Actions -->
        <div class="space-y-3">
            <button href=""
               class="inline-flex items-center justify-center w-full rounded-lg bg-emerald-600 px-4 py-2.5 cursor-pointer
                      text-sm font-medium text-white hover:bg-emerald-700 transition"
                      wire:loading.attr="disabled"
                      wire:click="sendVerificationEmail"
                      wire:target="sendVerificationEmail"
                      >
                <span wire:loading.remove>Resend Email</span>
                <x-loading-spinner />
            </button>
        </div>
        <p class="text-xs text-gray-500 mt-4">
                
                <a href="{{ route('logout') }}" class="text-emerald-600 hover:underline cursor-pointer">Logout</a>.
            </p>
    </div>
</div>
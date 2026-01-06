<x-layouts.auth>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-red-100 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Invitation Expired</h2>
            
            <p class="text-slate-600 mb-6">
                This invitation link has expired or is invalid. For security reasons, invitations are time-limited.
            </p>

            <div class="rounded-md bg-slate-50 p-4 mb-6 text-sm text-slate-500">
                Please contact your administrator to request a new invitation.
            </div>

            <a href="{{ route('login') }}" class="font-medium text-[#134E4A] hover:text-[#2DD4BF]">
                Return to Login
            </a>
        </div>
    </div>
</x-layouts.auth>

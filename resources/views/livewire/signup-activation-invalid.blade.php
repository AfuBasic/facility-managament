<div>
    <x-card class="w-full max-w-md text-center">

        <x-slot:header>
            <div class="flex justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-10 h-10 text-amber-500"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h1 class="text-lg font-semibold text-gray-900">
                Invalid or expired link
            </h1>

            <p class="mt-2 text-sm text-gray-600">
                This activation link is no longer valid.
            </p>
        </x-slot:header>

        <p class="text-sm text-gray-700">
            The link may have expired or already been used.
            You can request a new activation email below.
        </p>

    </x-card>
</div>
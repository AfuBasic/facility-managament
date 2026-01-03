<div>
    <x-card class="w-full max-w-md text-center">

        <x-slot:header class="">
            <div class="flex justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-10 h-10 text-emerald-600"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h1 class="text-lg font-semibold text-gray-900">
                Account activated
            </h1>

            <p class="mt-2 text-sm text-gray-600">
                Your email has been successfully verified.
            </p>
        </x-slot:header>

        <p class="text-sm text-gray-700">
            You now have full access to your account and can start using the platform.
        </p>

        <x-slot:footer class="">
            <div class="w-full flex justify-center">
            <a
                href="{{ route('login') }}"
                class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium
                       bg-emerald-600 text-white hover:bg-emerald-700 transition"
            >
                <span>Proceed</span>
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
            </div>

        </x-slot:footer>

    </x-card>
</div>
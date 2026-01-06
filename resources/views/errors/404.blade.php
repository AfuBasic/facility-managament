<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <!-- Error Icon -->
            <div class="mb-6">
                <div class="mx-auto w-24 h-24 bg-teal-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <h1 class="text-6xl font-bold text-slate-900 mb-2">404</h1>
            
            <!-- Error Message -->
            <h2 class="text-2xl font-semibold text-slate-700 mb-3">Page Not Found</h2>
            <p class="text-slate-500 mb-8">
                Sorry, we couldn't find the page you're looking for. It might have been moved or deleted.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="javascript:history.back()" class="inline-flex items-center justify-center px-6 py-3 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Go Back
                </a>
                <a href="{{ route('app.dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Go Home
                </a>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-slate-400 text-sm mt-6">
            &copy; {{ date('Y') }} Optima FM. All rights reserved.
        </p>
    </div>
</body>
</html>

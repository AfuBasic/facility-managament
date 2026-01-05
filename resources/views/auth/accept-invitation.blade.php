<x-layouts.auth>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-[#134E4A]">
            Welcome to the Team!
        </h2>
        <p class="mt-2 text-center text-sm text-slate-600">
            Set your password to accept the invitation for <strong>{{ $membership->clientAccount->name ?? 'Optima FM' }}</strong>.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-slate-100">
            <form action="{{ route('invitations.accept', ['membership' => $membership->id] + request()->query()) }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email Address</label>
                    <div class="mt-1">
                        <input id="email" type="email" value="{{ $membership->user->email }}" disabled class="appearance-none block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-[#2DD4BF] focus:border-[#2DD4BF] sm:text-sm bg-slate-50 text-slate-500">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">New Password</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required class="appearance-none block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-[#2DD4BF] focus:border-[#2DD4BF] sm:text-sm">
                    </div>
                    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm Password</label>
                    <div class="mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="appearance-none block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-[#2DD4BF] focus:border-[#2DD4BF] sm:text-sm">
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-[#134E4A] to-[#2DD4BF] hover:from-[#0f3f3c] hover:to-[#25b0a0] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2DD4BF] transition-all duration-300 transform hover:scale-[1.02]">
                        Set Password & Accept Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.auth>

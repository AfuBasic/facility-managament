<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * @var array<string>
     */
    protected array $providers = ['google'];

    public function redirect(string $provider): RedirectResponse
    {
        if (! in_array($provider, $this->providers)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        if (! in_array($provider, $this->providers)) {
            abort(404);
        }

        $socialUser = Socialite::driver($provider)->user();
        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($socialAccount) {
            $socialAccount->update([
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
                'avatar' => $socialUser->getAvatar(),
            ]);

            Auth::login($socialAccount->user);

            return redirect()->intended(route('user.home'));
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            $user->socialAccounts()->create([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
                'avatar' => $socialUser->getAvatar(),
            ]);

            Auth::login($user);

            return redirect()->intended(route('user.home'));
        }

        $user = User::create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
        ]);

        $user->email_verified_at = now();
        $user->save();

        $user->socialAccounts()->create([
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'provider_token' => $socialUser->token,
            'provider_refresh_token' => $socialUser->refreshToken,
            'avatar' => $socialUser->getAvatar(),
        ]);

        Auth::login($user);

        return redirect()->route('user.home');
    }
}

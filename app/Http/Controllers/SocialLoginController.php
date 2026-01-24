<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\SocialAccount;
use App\Models\User;
use App\Notifications\AdminNewUserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
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

            if ($socialAccount->user->suspended_at) {
                return redirect()->route('login')->with('error', 'Your account has been suspended. Please contact support.');
            }

            Auth::login($socialAccount->user);

            return redirect()->intended(route('user.home'));
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            if ($user->suspended_at) {
                return redirect()->route('login')->with('error', 'Your account has been suspended. Please contact support.');
            }

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

        // Notify all admins about the new user registration via social login
        Notification::send(Admin::all(), new AdminNewUserNotification($user, $provider));

        Auth::login($user);

        return redirect()->route('user.home');
    }
}

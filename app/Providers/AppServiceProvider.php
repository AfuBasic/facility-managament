<?php

namespace App\Providers;

use App\Models\ClientAccount;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RedirectIfAuthenticated::redirectUsing(function($request) {
            return route('user.home');
        });

        Gate::before(function ($user, $ability) {
            if (! app()->bound(ClientAccount::class)) {
                return null;
            }
            $clientAccount = app(ClientAccount::class);
            // Spatie scopes to the current team automatically via setPermissionsTeamId called in middleware
            return $user->hasRole('admin') ?: null;
        });
    }
}

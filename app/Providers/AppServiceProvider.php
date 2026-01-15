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
            // Admins bypass all checks EXCEPT delete for work orders
            if ($ability === 'delete' && request()->route('workOrder')) {
                return null; // Let the policy decide
            }
            
            return $user->hasRole('admin') ?: null;
        });
    }
}

<?php

namespace App\Providers;

use App\Models\WorkOrder;
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
        RedirectIfAuthenticated::redirectUsing(function ($request) {
            return route('user.home');
        });

        Gate::before(function ($user, $ability, $arguments) {
            // For work order specific actions, let the policy decide based on status
            if (! empty($arguments) && $arguments[0] instanceof WorkOrder) {
                if (in_array($ability, ['update', 'delete'])) {
                    return null; // Let the WorkOrderPolicy decide
                }
            }

            // Admins bypass all other checks
            return $user->hasRole('admin') ?: null;
        });
    }
}

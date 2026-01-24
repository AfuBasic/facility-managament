<?php

use App\Livewire\Admin\Clients;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Login;
use App\Livewire\Admin\Notifications;
use App\Livewire\Admin\Users;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// Guest routes (not logged in as admin)
Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', Login::class)->name('admin.login');
});

// Protected admin routes
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/users', Users::class)->name('users');
    Route::get('/clients', Clients::class)->name('clients');
    Route::get('/notifications', Notifications::class)->name('notifications');

    Route::get('/logout', function () {
        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('admin.login');
    })->name('logout');

    // Generate impersonation URL (returns signed URL to open in new tab)
    Route::get('/users/{user}/impersonate', function (User $user) {
        $url = URL::temporarySignedRoute(
            'admin.impersonate.login',
            now()->addMinutes(5),
            ['user' => $user->id]
        );

        return redirect()->away($url);
    })->name('users.impersonate');
});

// Handle impersonation login via signed URL (opens in new tab)
Route::get('/admin/impersonate/{user}', function (User $user) {
    if (! request()->hasValidSignature()) {
        abort(403, 'Invalid or expired impersonation link.');
    }

    Auth::login($user);
    session(['impersonating' => true]);

    return redirect()->route('user.home');
})->name('admin.impersonate.login');

// Stop impersonation - just logs out and closes tab
Route::middleware('auth')->get('/admin/stop-impersonating', function () {
    if (! session('impersonating')) {
        return redirect()->route('user.home');
    }

    Auth::logout();
    session()->forget('impersonating');
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login')->with('message', 'Impersonation session ended. You can close this tab.');
})->name('admin.stop-impersonating');

<?php

use App\Http\Controllers\ClientSessionController;
use App\Http\Controllers\SignupActivate;
use App\Http\Middleware\IsVerified;
use App\Http\Middleware\SetClientContext;
use App\Livewire\Client\Assets;
use App\Livewire\Client\Dashboard;
use App\Livewire\Client\Facilities;
use App\Livewire\Client\Roles;
use App\Livewire\Client\SlaPolicy;
use App\Livewire\Client\Users;
use App\Livewire\Client\Vendors;
use App\Livewire\Client\WorkOrders;
use App\Livewire\ForgotPassword;
use Illuminate\Support\Facades\Route;
use App\Livewire\Login;
use App\Livewire\ResetPassword;
use App\Livewire\SignedUp;
use App\Livewire\Signup;
use App\Livewire\SignupActivation;
use App\Livewire\UserHome;
use App\Livewire\UserSettings;
use App\Livewire\VerifyAccount;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', IsVerified::class])->group(function() {
    Route::get('/home', UserHome::class)->name('user.home'); 
    Route::get('/user/settings', UserSettings::class)->name('user.settings');
    
    // Switch Client Route
    Route::get('/app/switch/{client_id}', [ClientSessionController::class, 'switch'])->name('app.switch');

    // App Routes (Client Context)
    Route::middleware([SetClientContext::class])
        ->prefix('app')
        ->name('app.')
        ->group(function () {
             Route::get('/dashboard', Dashboard::class)->name('dashboard');
             Route::get('/facilities', Facilities::class)->name('facilities');
             Route::get('/assets', Assets::class)->name('assets');
             Route::get('/work-orders', WorkOrders::class)->name('work-orders');
             Route::get('/sla-policy', SlaPolicy::class)->name('sla-policy');
             Route::get('/vendors', Vendors::class)->name('vendors');
             Route::get('/users', Users::class)->name('users');
             Route::get('/roles', Roles::class)->name('roles');
    });
});

/**
* Activation Route
*/
Route::get('/activate/{user}', SignupActivation::class)->middleware('signed')->name('activate');

/**
* Guest Routes
*/
Route::get('/signed-up', SignedUp::class)->name('signed-up');
Route::get('/email/verify', VerifyAccount::class)->name('verification.notice');
Route::middleware('guest')->group(function() {
    Route::get('/signup', Signup::class)->name('signup');
    Route::get('/login', Login::class)->name('login');
    Route::get('/forgot-password', ForgotPassword::class )->name('forgot-password');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

/**
 * Logout Route
 */
Route::get('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');
<?php

use App\Http\Controllers\SignupActivate;
use App\Http\Middleware\IsVerified;
use App\Livewire\ForgotPassword;
use Illuminate\Support\Facades\Route;
use App\Livewire\Login;
use App\Livewire\SignedUp;
use App\Livewire\Signup;
use App\Livewire\SignupActivation;
use App\Livewire\UserHome;
use App\Livewire\VerifyAccount;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth',IsVerified::class])->group(function() {
    Route::get('/home', UserHome::class)->name('user.home'); 
});

/**
* Logout a user
*/
Route::get('/logout', function() {
    Auth::logout();
   return redirect('/login');
})->middleware('auth')->name('logout');

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
    Route::get('/reset-password/{token}', \App\Livewire\ResetPassword::class)->name('password.reset');
});


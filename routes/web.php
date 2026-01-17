<?php

use App\Http\Controllers\AcceptInvitationController;
use App\Http\Controllers\ClientSessionController;
use App\Http\Middleware\IsVerified;
use App\Http\Middleware\SetClientContext;
use App\Livewire\Client\AssetDetail;
use App\Livewire\Client\ContactGroups;
use App\Livewire\Client\Contacts;
use App\Livewire\Client\ContactTypes;
use App\Livewire\Client\Dashboard;
use App\Livewire\Client\Facilities;
use App\Livewire\Client\FacilityDetail;
use App\Livewire\Client\Notifications;
use App\Livewire\Client\Roles;
use App\Livewire\Client\Settings;
use App\Livewire\Client\SlaPolicy;
use App\Livewire\Client\StoreDetail;
use App\Livewire\Client\Users;
use App\Livewire\Client\Vendors;
use App\Livewire\Client\WorkOrderCreate;
use App\Livewire\Client\WorkOrderDetail;
use App\Livewire\Client\WorkOrderEdit;
use App\Livewire\Client\WorkOrderList;
use App\Livewire\ForgotPassword;
use App\Livewire\Login;
use App\Livewire\ResetPassword;
use App\Livewire\SignedUp;
use App\Livewire\Signup;
use App\Livewire\SignupActivation;
use App\Livewire\UserHome;
use App\Livewire\UserInvitations;
use App\Livewire\UserSettings;
use App\Livewire\VerifyAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', IsVerified::class])->group(function () {
    Route::get('/home', UserHome::class)->name('user.home');
    Route::get('/user/invitations', UserInvitations::class)->name('user.invitations');
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
            Route::get('/facilities/{facility}', FacilityDetail::class)->name('facilities.show');
            Route::get('/stores/{store}', StoreDetail::class)->name('store.detail');
            Route::get('/assets/{asset}', AssetDetail::class)->name('asset.detail');

            // Work Orders
            Route::get('/work-orders', WorkOrderList::class)->name('work-orders.index');
            Route::get('/work-orders/create', WorkOrderCreate::class)->name('work-orders.create');
            Route::get('/work-orders/{workOrder}/edit', WorkOrderEdit::class)->name('work-orders.edit');
            Route::get('/work-orders/{workOrder}', WorkOrderDetail::class)->name('work-orders.show');

            // Notifications
            Route::get('/notifications', Notifications::class)->name('notifications.index');

            Route::get('/sla-policy', SlaPolicy::class)->name('sla-policy');
            Route::get('/vendors', Vendors::class)->name('vendors');
            Route::get('/users', Users::class)->name('users');
            Route::get('/contacts', Contacts::class)->name('contacts');
            Route::get('/contacts/types', ContactTypes::class)->name('contacts.types');
            Route::get('/contacts/groups', ContactGroups::class)->name('contacts.groups');
            Route::get('/roles', Roles::class)->name('roles');
            Route::get('/settings', Settings::class)->name('settings');
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
Route::group(['middleware' => ['guest']], function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/signup', Signup::class)->name('signup');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');

    // Invitation Routes
});

Route::get('/invitations/{membership}/accept', [AcceptInvitationController::class, 'show'])->name('invitations.accept');
Route::post('/invitations/{membership}/accept', [AcceptInvitationController::class, 'store']);
/**
 * Logout Route
 */
Route::get('/logout', function () {
    Auth::logout();

    return redirect()->route('login');
})->name('logout');

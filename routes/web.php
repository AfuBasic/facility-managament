<?php

use App\Http\Controllers\AcceptInvitationController;
use App\Http\Controllers\ClientSessionController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Middleware\IsVerified;
use App\Http\Middleware\SetClientContext;
use App\Livewire\Client\AssetDetail;
use App\Livewire\Client\ContactGroups;
use App\Livewire\Client\Contacts;
use App\Livewire\Client\ContactTypes;
use App\Livewire\Client\Dashboard;
use App\Livewire\Client\Events\EventsIndex;
use App\Livewire\Client\Facilities;
use App\Livewire\Client\FacilityDetail;
use App\Livewire\Client\MessagesIndex;
use App\Livewire\Client\Notifications;
use App\Livewire\Client\Reports\Facilities\AssetConditionReport;
use App\Livewire\Client\Reports\Facilities\MaintenanceHistoryReport;
use App\Livewire\Client\Reports\Financial\CostSummaryReport;
use App\Livewire\Client\Reports\ReportsIndex;
use App\Livewire\Client\Reports\WorkOrders\SlaComplianceReport;
use App\Livewire\Client\Reports\WorkOrders\StatusDistributionReport;
use App\Livewire\Client\Reports\WorkOrders\TechnicianPerformanceReport;
use App\Livewire\Client\Roles;
use App\Livewire\Client\Settings;
use App\Livewire\Client\SlaPolicy;
use App\Livewire\Client\StoreDetail;
use App\Livewire\Client\Users;
use App\Livewire\Client\Vendors;
use App\Livewire\Client\WorkOrderDetail;
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

Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
Route::view('/terms-of-use', 'terms-of-use')->name('terms-of-use');

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

            Route::get('/work-orders', WorkOrderList::class)->name('work-orders.index');
            Route::get('/work-orders/{workOrder}', WorkOrderDetail::class)->name('work-orders.show');

            // Notifications
            Route::get('/notifications', Notifications::class)->name('notifications.index');

            // Messages
            Route::get('/messages', MessagesIndex::class)->name('messages.index');
            Route::get('/messages/{conversation}', MessagesIndex::class)->name('messages.show');

            // Events
            Route::get('/events', EventsIndex::class)->name('events.index');

            Route::get('/sla-policy', SlaPolicy::class)->name('sla-policy');
            Route::get('/vendors', Vendors::class)->name('vendors');
            Route::get('/users', Users::class)->name('users');
            Route::get('/contacts', Contacts::class)->name('contacts');
            Route::get('/contacts/types', ContactTypes::class)->name('contacts.types');
            Route::get('/contacts/groups', ContactGroups::class)->name('contacts.groups');
            Route::get('/roles', Roles::class)->name('roles');
            Route::get('/settings', Settings::class)->name('settings');

            // Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', ReportsIndex::class)->name('index');

                // Work Order Reports
                Route::get('/work-orders/status', StatusDistributionReport::class)->name('work-orders.status');
                Route::get('/work-orders/sla', SlaComplianceReport::class)->name('work-orders.sla');
                Route::get('/work-orders/technicians', TechnicianPerformanceReport::class)->name('work-orders.technicians');

                // Facility Reports
                Route::get('/facilities/maintenance', MaintenanceHistoryReport::class)->name('facilities.maintenance');
                Route::get('/facilities/assets', AssetConditionReport::class)->name('facilities.assets');

                // Financial Reports
                Route::get('/financial/costs', CostSummaryReport::class)->name('financial.costs');
            });
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

    // Social Login Routes
    Route::get('/auth/{provider}', [SocialLoginController::class, 'redirect'])->name('social.redirect');
    Route::get('/social-auth/{provider}', [SocialLoginController::class, 'callback'])->name('social.callback');
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

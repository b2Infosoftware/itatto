<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\LogController;
use App\Http\Controllers\V1\TagController;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\PlanController;
use App\Http\Controllers\V1\RoleController;
use App\Http\Controllers\V1\MediaController;
use App\Http\Controllers\V1\StaffController;
use App\Http\Controllers\V1\StripeController;
use App\Http\Controllers\V1\ProjectController;
use App\Http\Controllers\V1\ServiceController;
use App\Http\Controllers\V1\TimeOffController;
use App\Http\Controllers\V1\CampaignController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\CustomerController;
use App\Http\Controllers\V1\LocationController;
use App\Http\Controllers\V1\SettingsController;
use App\Http\Controllers\V1\DashboardController;
use App\Http\Controllers\V1\AppointmentController;
use App\Http\Controllers\V1\AvailablityController;
use App\Http\Controllers\V1\ConsentFormController;
use App\Http\Controllers\V1\OrganisationController;
use App\Http\Controllers\V1\SubscriptionController;
use App\Http\Controllers\V1\CalendarSettingsController;
use App\Http\Controllers\V1\CustomerVipController;
use App\Http\Controllers\V1\ExclusiveOfferController;
use App\Http\Controllers\V1\ManageBookingController;
use App\Http\Controllers\V1\NotificationTemplateController;
use App\Http\Controllers\V1\OnlineSettingController;
use App\Http\Controllers\V1\OnlineHourController;
use App\Http\Controllers\V1\PublicSlotController;
use App\Http\Controllers\V1\SettingPublicBookingController;
use App\Http\Controllers\V1\VipController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function () {
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/cancel-appointment/{id}', [AppointmentController::class, 'cancel'])->middleware('throttle:3,1');
    Route::get('/accept-invitation', [StaffController::class, 'acceptInvitation'])->name('accept.invitation');
    Route::post('/resend-invitation', [StaffController::class, 'resendInvitation']);

    Route::get('/newsletter/accept/{customer}', [CustomerController::class, 'acceptNewsletter'])->name('newsletter.approve');
    Route::get('/newsletter/reject/{customer}', [CustomerController::class, 'rejectNewsletter'])->name('newsletter.reject');

    // Public API Booking
    Route::get('/book/{slug}', [PublicSlotController::class, 'show']);
    Route::get('/book/is-open/{slug}', [PublicSlotController::class, 'getopen']);
    Route::post('/book/store', [PublicSlotController::class, 'store'])->name('booking.store');
    Route::get('/book/show-staff-time/{service}', [PublicSlotController::class, 'selectDateTime']);

    Route::group(['middleware' => 'guest'], function () {
        // Stripe Webhook
        Route::post('/stripe-webhook', [StripeController::class, 'webhook'])->name('stripeWebhook');

        // Auth related
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/google/redirect', [AuthController::class, 'loginGoogleRedirect']);
        Route::get('/google/callback', [AuthController::class, 'googleCallback']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
    });

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/verify-email', [AuthController::class, 'verifyEmail'])->name('verification.verify');

        // Auth/profile related routes
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/resend-verification', [AuthController::class, 'resendVerificationEmail']);
        Route::post('/me/shortlist', [AuthController::class, 'setStaffShortlist']);
        Route::post('/me/preferred-staff/{staff}', [AuthController::class, 'setPreferredStaff']);
        Route::post('/change-location/{location}', [AuthController::class, 'changeDefaultLocation']);

        Route::post('/change-organisation/{organisation}', [AuthController::class, 'changeDefaultOrganisation']);

        // Private API Booking
        Route::get('/book-list', [ManageBookingController::class, 'bookingList'])->name('booking.list');
        Route::patch('/book/approved/{publicSlot}', [ManageBookingController::class, 'approve'])->name('booking.approve');
        Route::patch('/book/rejected/{publicSlot}', [ManageBookingController::class, 'rejected'])->name('booking.rejected');
        Route::patch('/book/reschedule/{publicSlot}', [ManageBookingController::class, 'reschedule'])->name('booking.reschedule');
        Route::delete('/book/{publicSlot}', [ManageBookingController::class, 'destroy'])->name('booking.destroy');

        Route::get('/show-setting-public-booking', [SettingPublicBookingController::class, 'show'])->name('show.setting.booking');
        Route::post('/setting-public-booking', [SettingPublicBookingController::class, 'createOrUpdate'])->name('create.setting.booking');

        Route::patch('/sync-online-hour', [OnlineHourController::class, 'syncOnlineHours']);
        Route::post('/online-hour', [OnlineHourController::class, 'store']);
        Route::get('/staff/{staffId}/online-hours', [OnlineHourController::class, 'getOnlineHoursByStaff']);

        // VIP CUSTOMER
        Route::post('/setvip', [CustomerVipController::class, 'setvip']);

        // Organisations
        Route::get('/organisations', [OrganisationController::class, 'index']);
        Route::get('/organisations/super-admin', [OrganisationController::class, 'superAdminOrganisations']);
        Route::post('/organisations/suspend/{organisation}', [OrganisationController::class, 'suspend']);
        Route::patch('/organisations/{organisation}', [OrganisationController::class, 'update']);
        Route::patch('/organisation-notifications/{notificationSettings}', [OrganisationController::class, 'updateNotificationSettings']);

        // Locations
        Route::get('/locations', [LocationController::class, 'index']);
        Route::post('/locations', [LocationController::class, 'store']);
        Route::patch('/locations/{location}', [LocationController::class, 'update']);
        Route::delete('/locations/{location}', [LocationController::class, 'destroy']);

        // vip
        Route::get('/vip', [VipController::class, 'index']);
        Route::post('/vip', [VipController::class, 'store']);
        Route::get('/vip/{vip}', [VipController::class, 'show']);
        Route::patch('/vip/{vip}', [VipController::class, 'update']);
        Route::delete('/vip/{vip}', [VipController::class, 'destroy']);

        Route::patch('/sync-exclusive-offer-vip/{vip}', [VipController::class, 'syncExclusiveOffer']);

        // Exclusive Offer
        Route::get('/exclusive-offer', [ExclusiveOfferController::class, 'index']);
        Route::post('/exclusive-offer', [ExclusiveOfferController::class, 'store']);
        Route::get('/exclusive-offer/{exclusive_offer}', [ExclusiveOfferController::class, 'show']);
        Route::patch('/exclusive-offer/{exclusive_offer}', [ExclusiveOfferController::class, 'update']);
        Route::delete('/exclusive-offer/{exclusive_offer}', [ExclusiveOfferController::class, 'destroy']);

        // Staff
        Route::get('/staff', [StaffController::class, 'index']);
        Route::get('/staff/{staff}', [StaffController::class, 'show']);
        Route::post('/staff', [StaffController::class, 'store']);
        Route::patch('/staff/{staff}', [StaffController::class, 'update']);
        Route::delete('/staff/{staff}', [StaffController::class, 'destroy']);
        Route::patch('/sync-services/{staff}', [StaffController::class, 'syncServices']);

        // Tag
        Route::get('/tags', [TagController::class, 'index']);
        Route::post('/tags', [TagController::class, 'store']);
        Route::delete('/tags/{tag}', [TagController::class, 'destroy']);

        // Susbcriptions
        Route::get('/subscriptions', [SubscriptionController::class, 'index']);

        // Categories
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        // Services
        Route::get('/services', [ServiceController::class, 'index']);
        Route::get('/services/{service}', [ServiceController::class, 'show']);
        Route::post('/services', [ServiceController::class, 'store']);
        Route::post('/services/reorder', [ServiceController::class, 'changeOrder']);
        Route::patch('/services/{service}', [ServiceController::class, 'update']);
        Route::delete('/services/{service}', [ServiceController::class, 'destroy']);

        // Calendar Settings
        Route::get('/calendar-settings', [CalendarSettingsController::class, 'show']);
        Route::patch('/calendar-settings/{calendarSettings}', [CalendarSettingsController::class, 'update']);

        // Customers
        Route::get('/customers', [CustomerController::class, 'index']);
        Route::get('/customers/{customer}', [CustomerController::class, 'show']);
        Route::get('/customers/{customer}/stats', [CustomerController::class, 'getStats']);
        Route::post('/customers', [CustomerController::class, 'store']);
        Route::patch('/customers/{customer}', [CustomerController::class, 'update']);
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy']);
        Route::post('/customers/export', [CustomerController::class, 'exportCustomers']);

        // Media
        Route::post('/media/upload', [MediaController::class, 'upload']);
        Route::delete('/media/{media}', [MediaController::class, 'delete']);

        // Availability
        Route::get('/availabilities', [AvailablityController::class, 'index']);
        Route::post('/availabilities', [AvailablityController::class, 'store']);
        Route::patch('/availabilities/{availability}', [AvailablityController::class, 'update']);
        Route::delete('/availabilities/{availability}', [AvailablityController::class, 'destroy']);

        // Time Off
        Route::get('/time-off/{timeOff}', [TimeOffController::class, 'show']);
        Route::post('/time-off', [TimeOffController::class, 'store']);
        Route::patch('/time-off/{timeOff}', [TimeOffController::class, 'update']);
        Route::delete('/time-off/{timeOff}', [TimeOffController::class, 'destroy']);

        // Projects
        Route::get('/projects', [ProjectController::class, 'index']);
        Route::post('/projects', [ProjectController::class, 'store']);
        Route::post('/projects/save-document', [ProjectController::class, 'saveDocument']);
        Route::patch('/projects/{project}', [ProjectController::class, 'update']);
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);

        // Roles
        Route::get('/roles', [RoleController::class, 'index']);
        Route::get('/roles/permissions', [RoleController::class, 'getAllPermissions']);
        Route::get('/roles/{role}', [RoleController::class, 'show']);
        Route::post('/roles', [RoleController::class, 'store']);
        Route::patch('/roles/{role}', [RoleController::class, 'update']);
        Route::delete('/roles/{role}', [RoleController::class, 'destroy']);

        // Appointments
        Route::get('/appointments', [AppointmentController::class, 'index']);
        Route::get('/appointments/upcoming', [AppointmentController::class, 'searchUpcoming']);
        Route::get('/appointments/{appointment}', [AppointmentController::class, 'show']);
        Route::post('/appointments', [AppointmentController::class, 'store']);
        Route::patch('/appointments/change-status/{appointment}', [AppointmentController::class, 'changeStatus']);
        Route::post('/appointments/duplicate/{appointment}', [AppointmentController::class, 'duplicate']);
        Route::patch('/appointments/{appointment}', [AppointmentController::class, 'update']);
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy']);

        // Consent Forms
        Route::get('/consent-forms/for-category/{category_id}', [ConsentFormController::class, 'forCategory']);
        Route::get('/consent-forms', [ConsentFormController::class, 'index']);
        Route::get('/consent-forms/{consentForm}', [ConsentFormController::class, 'show']);
        Route::post('/consent-forms', [ConsentFormController::class, 'store']);
        Route::patch('/consent-forms/{consentForm}', [ConsentFormController::class, 'update']);
        Route::delete('/consent-forms/{consentForm}', [ConsentFormController::class, 'destroy']);

        // Email Templates
        Route::get('/notification-templates', [NotificationTemplateController::class, 'index']);
        Route::get('/notification-templates/{notificationTemplate}', [NotificationTemplateController::class, 'show']);
        Route::patch('/notification-templates/{notificationTemplate}', [NotificationTemplateController::class, 'update']);

        // Pricing Plans
        Route::get('/plans', [PlanController::class, 'index']);
        Route::post('/plans/buy-sms', [PlanController::class, 'buySMS']);
        Route::post('/plans/{plan}/subscribe', [PlanController::class, 'subscribe']);
        Route::post('/plans/{plan}/subscribe/trial', [PlanController::class, 'trial']);
        Route::post('/plans/{plan}/cancel', [PlanController::class, 'cancel']);
        Route::get('/plans/trial', [PlanController::class, 'indexTrial']);
        Route::get('/plans/check', [PlanController::class, 'checkTrial']);

        // Marketing
        Route::post('/campaigns/count-customers', [CampaignController::class, 'countCustomers']);
        Route::post('/campaigns/preview', [CampaignController::class, 'preview']);
        Route::get('/campaigns', [CampaignController::class, 'index']);
        Route::post('/campaigns', [CampaignController::class, 'store']);
        Route::get('/campaigns/{campaign}', [CampaignController::class, 'show']);
        Route::patch('/campaigns/{campaign}', [CampaignController::class, 'update']);
        Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy']);

        //Logs
        Route::get('/logs', [LogController::class, 'index']);
    });
});

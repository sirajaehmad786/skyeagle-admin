<?php

use App\Http\Controllers\Crm\ActivityController;
use App\Http\Controllers\Crm\BookingController;
use App\Http\Controllers\Crm\CategoryController;
use App\Http\Controllers\Crm\ContactController;
use App\Http\Controllers\Crm\CustomerReviewController;
use App\Http\Controllers\Crm\DashboardController;
use App\Http\Controllers\Crm\DocumentController;
use App\Http\Controllers\Crm\EnquiryController;
use App\Http\Controllers\Crm\ProfileController;
use App\Http\Controllers\Crm\RoleController;
use App\Http\Controllers\Crm\UserController;
use App\Http\Controllers\Crm\LocationController;
use App\Http\Controllers\Crm\Master\HotelController;
use App\Http\Controllers\Crm\Master\SightSeeingController;
use App\Http\Controllers\Crm\LeadController;
use App\Http\Controllers\Crm\MediaController;
use App\Http\Controllers\Crm\NotificationController;
use App\Http\Controllers\Crm\PackageController;
use App\Http\Controllers\Crm\PaymentController;
use App\Http\Controllers\Crm\QuotationController;
use App\Http\Controllers\Crm\SettingController;
use App\Http\Controllers\RoutingController;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Route;


require __DIR__.'/auth.php';


Route::get('pdf-test/{lead_id}/{quotation_id}/{test}',[QuotationController::class, 'exportPdf']);

Route::group(['prefix' => '/', 'middleware'=>['auth', 'check.active']], function () {

    //Media routes
    Route::resource("media", MediaController::class);

    //Package routes
    Route::resource("package", PackageController::class);
    
    //Category routes
    Route::resource("category", CategoryController::class);

    //Enquiry routes
    Route::resource("enquiry", EnquiryController::class);
    
    //CustomerReview
    Route::resource('customer-review', CustomerReviewController::class);
    
    
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::POST('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::POST('profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    Route::get('/get-city-details/{id}', [LocationController::class, 'getCityDetails'])->name('city.details');
    Route::get('/cities/geoapify-search', [LocationController::class, 'searchGeoapifyCities'])->name('cities.geoapify.search');

    Route::resource("roles", RoleController::class);
    Route::resource("users", UserController::class);

    
    //Contact routes
    Route::resource("contact", ContactController::class);
    Route::POST("contact/import", [ContactController::class, 'import'])->name('contact.import');
    Route::POST('contact/assign/user', [ContactController::class, 'assign'])->name('contact.assign');
    Route::post('contact/{contact}/generate-lead', [ContactController::class, 'generateLead'])
        ->name('contact.generateLead');


    Route::post('/notifications/read/{id}', [NotificationController::class, 'readSingle'])->name('notifications.read.single');

    // ✅ TEST NOTIFICATION ROUTE
    Route::get('test-notification', function () {
        auth()->user()->notify(new SystemNotification());
        return 'Notification added';
    })->name('test.notification');

    Route::get('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read.all');
    
    //Leads
    Route::resource("leads", LeadController::class);
    Route::POST("leads/store-follow-up", [LeadController::class, 'storeFollowUp'])->name('store.follow.up');
    Route::POST("leads/get-followup-list", [LeadController::class, 'followUpList'])->name('follow.up.list');
    Route::get('/lead/destination/{type}', [LeadController::class, 'getDestinationRow']);
    Route::get('/get-city-by-state/{state_id}', [LeadController::class, 'getCityByStateId']);
    Route::get('/change/destination/{type}', [LeadController::class, 'changeDestination']);
    Route::get('/get-city-by-country/{country_id}', [LeadController::class, 'getCityByCountryId']);
    Route::post('/lead-transfer',[LeadController::class, 'transfer'])->name('leads.transfer');
    Route::get('/lead/details/{id}', [LeadController::class, 'leadDetails'])
    ->name('lead.details');
    
    //Setting routes
    Route::resource('/settings', SettingController::class);
    
    //Activity routes
    Route::resource('/activities', ActivityController::class);
    
    // Booking
    Route::resource('/bookings', BookingController::class);
    Route::post('/bookings/store', [BookingController::class, 'store'])->name('bookings.store');

    //Notification routes
    Route::resource('/notifications', NotificationController::class);

    //Documents routes
    Route::resource('/documents', DocumentController::class);
   
    Route::get('documents/download/{contactId}',[DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/contact/{contactId}', [DocumentController::class, 'getByContact'])->name('documents.getByContact');
    //Quotation routes
    Route::resource("quotations", QuotationController::class);
    Route::get('quotations/create/{lead_id?}', [QuotationController::class, 'create'])->name('quotations.create.from.lead');
    Route::get('quotations/edit/{quotation_id}/items/{lead_id}', [QuotationController::class, 'edit'])->name('quotations.items.edit');
    Route::post('quotations/{quotation}/reset-tab-section', [QuotationController::class, 'resetTabSection'])
        ->name('quotations.reset-tab-section');
    Route::get('quotations/by-lead/{lead}', [QuotationController::class, 'getByLead'])->name('quotations.byLead');
    Route::post('/quotation/update-discount', [QuotationController::class, 'updateDiscount'])
    ->name('quotation.updateDiscount');


    //Quotation flight routes
    Route::POST('quotations/flight/save', [QuotationController::class, 'flightSave'])->name('flight.save');
    Route::POST('quotations/add-multi-city-row', [QuotationController::class, 'addMultiCityRow'])->name('add.multi-city.row');

    //Quotation visa routes
    Route::post('/visa/add-row', [QuotationController::class, 'addVisaRow'])->name('visa.add.row');
    Route::POST('quotations/visa/store', [QuotationController::class, 'visaStore'])->name('visa.store');

    //Quotation sightseeing route
    Route::POST('quotations/sightseeing/store', [QuotationController::class, 'sightseeingStore'])->name('sightseeing.store');
    Route::POST('quotations/sightseeing/add-sightseeing', [QuotationController::class, 'sightseeingAdd'])->name('add.new-sightseeing');
    Route::post('quotations/add-sub-sightseeing-row', [QuotationController::class, 'addSubSightseeingRow'])->name('add.sub.sightseeing.row');
    Route::get('sightseeing/title-suggestions', [QuotationController::class, 'getTitleSuggestions'])->name('sightseeing.title.suggestions');
    Route::get('sightseeing/selected-item-title-suggestions', [QuotationController::class, 'getDataBySelectTitle']);

    //Quotation hotel
    Route::post('quotation/hotel/add-row', [QuotationController::class, 'hotelAdd'])
    ->name('quotation.hotel.add-row');
    Route::post('quotations/hotel/save', [QuotationController::class, 'hotelStore'])->name('quotations.hotel.save');

    //pdf generate
    Route::get('quotations/{lead_id}/export-pdf/{quotation_id}', [QuotationController::class, 'exportPdf'])->name('quotations.exportPdf');
    Route::get('quotations/{lead_id}/preview-pdf/{quotation_id}', [QuotationController::class, 'previewQuotationPdf'])->name('quotations.previewPdf');

    Route::get('bookings/{lead_id}/export-pdf/{quotation_id}', [QuotationController::class, 'exportbookingPdf'])->name('booking.exportbookingPdf');
    
    //Hotels routes
    Route::get('/get-cities/{state_id}', [HotelController::class, 'getCitiesByState'])->name('get.cities.by.state');
    Route::resource("hotels", HotelController::class);

    //Sightseeing Master
    Route::resource("sightseeings", SightSeeingController::class);

    //Payment Routes
    Route::resource("payments", PaymentController::class);
    Route::get('payments/download/{id}', [PaymentController::class, 'downloadPdf'])->name('payments.downloadPdf');
    Route::post('payments/history', [PaymentController::class, 'paymentHistory'])->name('payment.history');
    
    Route::get('', [RoutingController::class, 'index'])->name('root');
    // Route::get('/dashboard', fn()=>view('index'))->name('dashboard');
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');

    Route::get('/get-states/{countryId}', [LeadController::class, 'getStates'])->name('get.states');
    Route::get('/cities', [LeadController::class, 'cities']);
    
    
});


<?php

use App\Http\Controllers\Crm\ActivityController;
use App\Http\Controllers\Crm\BlogCommentController;
use App\Http\Controllers\Crm\BlogPostController;
use App\Http\Controllers\Crm\CategoryController;
use App\Http\Controllers\Crm\CustomerReviewController;
use App\Http\Controllers\Crm\DestinationController;
use App\Http\Controllers\Crm\DashboardController;
use App\Http\Controllers\Crm\EnquiryController;
use App\Http\Controllers\Crm\ProfileController;
use App\Http\Controllers\Crm\UserController;
use App\Http\Controllers\Crm\LocationController;
use App\Http\Controllers\Crm\MediaController;
use App\Http\Controllers\Crm\NewsletterSubscriberController;
use App\Http\Controllers\Crm\NotificationController;
use App\Http\Controllers\Crm\PackageAttributeController;
use App\Http\Controllers\Crm\PackageController;
use App\Http\Controllers\Crm\SettingController;
use App\Http\Controllers\Crm\TourBookingRequestController;
use App\Http\Controllers\RoutingController;
use Illuminate\Support\Facades\Route;


require __DIR__.'/auth.php';

Route::group(['prefix' => '/', 'middleware'=>['auth', 'check.active']], function () {

    //Media routes
    Route::resource("media", MediaController::class);

    //Destination routes
    Route::resource("destinations", DestinationController::class)->except(['show']);

    //Package routes
    Route::resource("package-attributes", PackageAttributeController::class)->except(['show']);
    Route::resource("package", PackageController::class);
    
    //Category routes
    Route::get('category/search', [CategoryController::class, 'search'])->name('category.search');
    Route::resource("category", CategoryController::class);

    //Blog routes
    Route::patch('blog-posts/{blog_post}/inline-update', [BlogPostController::class, 'inlineUpdate'])->name('blog-posts.inline-update');
    Route::resource("blog-posts", BlogPostController::class);
    Route::patch('blog-comments/{blog_comment}/approval', [BlogCommentController::class, 'approval'])->name('blog-comments.approval');
    Route::resource("blog-comments", BlogCommentController::class)->only(['index', 'destroy']);

    //Enquiry routes
    Route::resource("enquiry", EnquiryController::class);

    //Tour booking request routes
    Route::resource('tour-booking-requests', TourBookingRequestController::class)->only(['index', 'show', 'update', 'destroy']);
    
    //CustomerReview
    Route::resource('customer-review', CustomerReviewController::class);

    //Subscriber routes
    Route::resource('newsletter-subscribers', NewsletterSubscriberController::class);
    
    
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::POST('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::POST('profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    Route::get('/get-city-details/{id}', [LocationController::class, 'getCityDetails'])->name('city.details');
    Route::get('/cities/geoapify-search', [LocationController::class, 'searchGeoapifyCities'])->name('cities.geoapify.search');

    Route::resource("users", UserController::class);

    Route::post('/notifications/read/{id}', [NotificationController::class, 'readSingle'])->name('notifications.read.single');

    Route::get('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read.all');
    
    //Setting routes
    Route::resource('/settings', SettingController::class);
    
    //Activity routes
    Route::resource('/activities', ActivityController::class);

    //Notification routes
    Route::resource('/notifications', NotificationController::class);
    
    Route::get('', [RoutingController::class, 'index'])->name('root');
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');
});


<?php 

return [
    'app_name' => env('APP_NAME'),
    'date_format' => 'd-m-Y',
    'user_status' => [
        "Active" => "Active",
        'Inactive' => "Inactive"
    ],

    'select_text'=> "-Select-",

    'booking_type' => [
        'Domestic',
        'International'
    ],

    'module' => [
        'Home',
        'Login With OTP',
        'Login With Password',
        'Tour Package',
        "Newsletter Subscribe",
        "Blog",
        "Flight",
        "Tour Details"
    ],

    'section' => [
        'Slider',
        'Banner',
        'Sidebar',
        'Popup',
    ],

    'media_upload' => [
        'max_files' => 10,
    ],

    'status'=>[
        'Active',
        'Inactive'
    ],

    'package_attribute_types' => [
        'popular' => 'Popular',
        'accommodation' => 'Accommodation Type',
        'activity' => 'Activities',
        'meal_plan' => 'Meal Plans'
    ],

    'blog_comment_status' => [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ],

    'tour_booking_request_status' => [
        'pending' => 'Pending',
        'contacted' => 'Contacted',
        'confirmed' => 'Confirmed',
        'cancelled' => 'Cancelled',
    ],
];

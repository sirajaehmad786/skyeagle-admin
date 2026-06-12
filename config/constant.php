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
        'Footer',
        'Ads' ,
        'Header',
        'Tour Package',
        "Newsletter Subscribe"
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

    'blog_comment_status' => [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ],
];

<?php 

return [
    'app_name' => env('APP_NAME'),
    'super_admin_role' => 'Super Admin',
    'rupee_symbol' => '₹',
    'date_format' => 'd-m-Y',
    'user_status' => [
        "Active" => "Active",
        'Inactive' => "Inactive"
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact Status (use config('constant.contact_status') or ContactStatus::*)
    |--------------------------------------------------------------------------
    */
    'contact_status' => [
        'active'   => 'active',
        'inactive' => 'inactive',
        'block'    => 'block',
    ],

    'initial' => [
        'MR.',
        'MRS.',
        'MS.',
        'DR.',
    ],

    'lead_source' => [
        'Website',
        'Facebook',
        'Instagram',
        'WhatsApp',
        'Direct Call',
        'Reference',
        'Walk-In',
    ],

    'marital_status' => [
        'NA',
        'Married',
        'Unmarried'
    ],

    'hotel_category' => [
        '1 Star',
        '2 Star',
        '3 Star',
        '4 Star',
        '5 Star',
    ],

    'food_preference' => [
        'Veg',
        'Non-Veg',
        'Jain Swaminarayan'
    ],

    'contact_meals' => [
        'APAI(Breakfast Lunch & Dinner)', 
		'MAPAI(Breakfast & Dinner)',
		'CPAI(Breakfast Only)',
		'EPAI(Room Only)' 
    ],
    'international_meals'=>[
        'BB CPAI(Breakfast Only)',
        'HB MAPAI(Breakfast & Dinner)',
        'FB APAI(Breakfast Lunch & Dinner)',
        'EPAI (Room only)'
    ],

    'customer_category' => [
        'Economy',
        'Standard',
        'Premium',
        'Luxury'
    ],

    'flight_requirements' => [
        'Yes',
        'No'
    ],
    
    'visa_requirements' => [
        'Yes',
        'No'
    ],

    'hotel_requirements' => [
        'Yes',
        'No'
    ],

    'sightseeing_requirements' => [
        'Yes',
        'No'
    ],

    'lead_stage' => [
        'New',
        'Contacted',
        'Quotation',
        'In Discussion',
        'On Hold',
        'Confirmed',
        'Lost'
    ],

    'lead_status' => [
        'Hot',
        'Warm',
        'Cold',
        'Follow-up Required',
        'No Response',
        'Confirmed',
        'Closed-Lost',
        'Closed-Won'
    ],

    'travel_type' => [
        'Domestic',
        'International'
    ],

    'flight_class' => [
        'Economy',
        'Premium Economy',
        'Business',
        'First'
    ],

    'companies' => [
        '1' => 'Sky Eagle'
    ],
    'trip_type' => [
        'one_way' => 'One Way',
        'round_trip' => 'Round Trip',
        'multi_city' => 'Multi City'
    ],
    'select_text'=> "-Select-",
    'booking'=>"SKB",
    'booking_status' => [
        'confirmed' => 'Confirmed',
        'on_trip'   => 'On Trip',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    /** Statuses that count as "confirmed" (revenue / non-cancelled) for dashboard. */
    'booking_status_confirmed' => ['confirmed', 'on_trip', 'completed'],
    'query_type'=>[
        'fit' => 'FIT',
        'git' => 'GIT'
    ],
    'payment_method'=>[
        'Gpay',
        "Bank Transfer",
        "Cash"
    ],

    'role_level' => [
        1,2,3,4,5
    ],
    'payment'=>'PYB',
   
    'choose_travel_class'=>[
        'Economy',
        'Premium Economy',
        'Business',
        'First Class'
    ],
    'visa_category'=>[
        'E-Visa',
        'Visa on Arrival',
        'ETA - electronic Travel Authorization',
        'Sticker Visa',
        'PAR - Pre Arrival Registration',
        'DAC - Digital Arrival Card'
    ],
    'visa_type'=>[
        'Single',
        'Multiple'
    ],
    'lead_code_prefix' => 'SET'
];
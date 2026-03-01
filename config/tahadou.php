<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp API Configuration
    |--------------------------------------------------------------------------
    */
    'whatsapp' => [
        'api_url'   => env('WHATSAPP_API_URL'),
        'api_token' => env('WHATSAPP_API_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Predefined Gift Interest Categories
    |--------------------------------------------------------------------------
    | Key => Display Label
    */
    'interests' => [
        'books'       => '📚 Books',
        'electronics' => '📱 Electronics & Gadgets',
        'sports'      => '🏋️ Sports & Fitness',
        'fashion'     => '👗 Fashion & Accessories',
        'home'        => '🏠 Home & Kitchen',
        'games'       => '🎮 Games & Entertainment',
        'beauty'      => '💄 Beauty & Skincare',
        'travel'      => '✈️ Travel & Outdoor',
        'art'         => '🎨 Art & Crafts',
        'food'        => '🍫 Food & Sweets',
    ],

];

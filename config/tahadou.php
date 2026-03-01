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
    | Keys only — labels live in lang/{locale}/app.php as interest_{key}
    */
    'interests' => [
        'books',
        'electronics',
        'sports',
        'fashion',
        'home',
        'games',
        'beauty',
        'travel',
        'art',
        'food',
    ],

];

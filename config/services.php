<?php
return [
    'paypal' => [
        'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID'),
        'secret' => env('PAYPAL_SANDBOX_SECRET'),
        'mode' => env('PAYPAL_MODE', 'sandbox'),  // 'sandbox' or 'live'
    ],
];

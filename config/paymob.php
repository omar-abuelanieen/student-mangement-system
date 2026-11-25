<?php

return [
    'base_url'        => env('PAYMOB_BASE_URL', 'https://accept.paymob.com'),
    'secret_key'      => env('PAYMOB_SECRET_KEY'),
    'public_key'      => env('PAYMOB_PUBLIC_KEY'),
    'hmac_secret'     => env('PAYMOB_HMAC_SECRET'),
    'integration_ids' => array_filter(
        array_map('intval', explode(',', env('PAYMOB_INTEGRATION_IDS', '')))
    ),
    'currency'        => 'EGP',
    'notification_url' => env('PAYMOB_NOTIFICATION_URL'),
    'redirection_url'  => env('PAYMOB_REDIRECTION_URL'),
];

<?php

use Illuminate\Support\Facades\Route;

Route::get('/payment/result', function () {
    return response(
        '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Payment</title></head>'
        . '<body style="font-family:sans-serif;text-align:center;margin-top:80px">'
        . '<h2>Thank you</h2>'
        . '<p>We received your payment attempt and are confirming it now.</p>'
        . '<p>You can close this page and check your invoice status in the app.</p>'
        . '</body></html>'
    );

});

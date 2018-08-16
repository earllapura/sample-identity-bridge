<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Gateway URI
    |--------------------------------------------------------------------------
    |
    | This value is the URL of the API gateway. This value is used for
    | redirects in OAuth2 authorize endpoint.
    |
    */

    'gateway' => env('API_GATEWAY_URI', 'http://localhost'),
];

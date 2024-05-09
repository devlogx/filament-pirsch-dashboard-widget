<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pirsch Client id & Client secret
    |--------------------------------------------------------------------------
    |
    | You can acquire your client id and secret id under
    | https://dashboard.pirsch.io/settings/integration
    |
    */
    'client_id' => env('PIRSCH_CLIENT_ID', null),
    'client_secret' => env('PIRSCH_CLIENT_SECRET', null),

    /*
    |--------------------------------------------------------------------------
    | Stats cache ttl
    |--------------------------------------------------------------------------
    |
    | This value is the ttl for the displayed dashboard
    | stats values. You can increase or decrease
    | this value.
    |
    */
    'cache_time' => 300,
];

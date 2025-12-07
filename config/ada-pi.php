<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ADA-Pi Device JWT Secret
    |--------------------------------------------------------------------------
    |
    | Secret folosit pentru semnarea/verificarea JWT-urilor de la device-uri.
    | IMPORTANT: Trebuie să fie IDENTIC cu cel din /etc/ada_pi/config.json
    | pe fiecare device Pi.
    |
    */

    'jwt_secret' => env('ADA_PI_JWT_SECRET', env('JWT_SECRET')),

    /*
    |--------------------------------------------------------------------------
    | Device Registration Mode
    |--------------------------------------------------------------------------
    |
    | 'open' - Orice device nou poate să se înregistreze (devine pending)
    | 'closed' - Doar device-uri pre-înregistrate pot comunica
    |
    */

    'registration_mode' => env('ADA_PI_REGISTRATION_MODE', 'open'),

    /*
    |--------------------------------------------------------------------------
    | Telemetry Settings
    |--------------------------------------------------------------------------
    */

    'telemetry' => [
        // Câte snapshot-uri să păstreze per device (0 = unlimited)
        'max_snapshots_per_device' => env('ADA_PI_MAX_SNAPSHOTS', 1000),
        
        // După câte zile să șteargă telemetria veche (0 = never)
        'retention_days' => env('ADA_PI_TELEMETRY_RETENTION', 90),
    ],

];

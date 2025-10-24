<?php
declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Slot JSON Delivery Strategy
    |--------------------------------------------------------------------------
    |
    | This determines how JSON slot files are delivered. Options:
    | - 'local': Write to local file system (development/production)
    | - 'remote_api': POST to remote API endpoint (production)
    | - 's3': Upload to S3 bucket (future implementation)
    |
    */
    'delivery_strategy' => env('SLOT_JSON_DELIVERY_STRATEGY', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Local File Strategy Configuration
    |--------------------------------------------------------------------------
    */
    'local' => [
        'primary_path' => env('SLOT_JSON_PRIMARY_PATH', 'api/slots'), // Relative to public_path()
        'backup_path' => env('SLOT_JSON_BACKUP_PATH', 'app/public/slots'), // Relative to storage_path()
        'enable_backup' => env('SLOT_JSON_ENABLE_BACKUP', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Remote API Strategy Configuration
    |--------------------------------------------------------------------------
    */
    'remote_api' => [
        'url' => env('SLOT_JSON_REMOTE_API_URL'),
        'api_key' => env('SLOT_JSON_REMOTE_API_KEY'),
        'timeout' => env('SLOT_JSON_REMOTE_TIMEOUT', 30),
        'retries' => env('SLOT_JSON_REMOTE_RETRIES', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | S3 Strategy Configuration (Future Implementation)
    |--------------------------------------------------------------------------
    */
    's3' => [
        'bucket' => env('SLOT_JSON_S3_BUCKET'),
        'region' => env('SLOT_JSON_S3_REGION', 'us-east-1'),
        'path' => env('SLOT_JSON_S3_PATH', 'slots/'),
        'public' => env('SLOT_JSON_S3_PUBLIC', true),
    ],
];
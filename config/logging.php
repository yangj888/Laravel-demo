<?php

use Monolog\Formatter\JsonFormatter;

return [

    'default' => env('LOG_CHANNEL', 'json'),

    'channels' => [

        'json' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.json.log'),
            'level' => 'info',
            'formatter' => JsonFormatter::class,
        ],

    ],
];


<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2020 LYRASOFT.
 * @license    MIT
 */

declare(strict_types=1);

use Windwalker\Core\Provider\DateTimeProvider;

return [
    'secret' => 'Yfp1bUBPO2d6L3t1zMTJ8Q',

    'name' => 'ShopGo',

    'debug' => (bool) (env('APP_DEBUG') ?? false),

    'mode' => env('APP_ENV', 'prod'),

    'timezone' => env('APP_TIMEZONE', 'Asia/Taipei'),

    'server_timezone' => env('APP_SERVER_TIMEZONE', 'UTC'),

    'dump_server' => [
        'host' => env('DUMP_SERVER_HOST') ?: 'tcp://127.0.0.1:9912'
    ],

    'providers' => [
        DateTimeProvider::class
    ]
];

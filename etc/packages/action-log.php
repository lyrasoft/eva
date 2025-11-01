<?php

declare(strict_types=1);

use Lyrasoft\ActionLog\ActionLogPackage;

return [
    'action_log' => [
        'reserve_max_time' => env('ACTION_LOG_MAX_TIME') ?: '3months',
        'auth_clear' => [
            'chance' => (int) env('ACTION_LOG_CLEAR_CHANCE', '1'),
            'chance_base' => (int) env('ACTION_LOG_CLEAR_CHANCE_BASE', '100')
        ],

        'providers' => [
            ActionLogPackage::class
        ],
        'bindings' => [
            //
        ]
    ]
];

<?php

declare(strict_types=1);

use Lyrasoft\ActionLog\ActionLogPackage;
use Windwalker\Core\Attributes\ConfigModule;

return #[ConfigModule('action_log', enabled: true, priority: 100, belongsTo: ActionLogPackage::class)]
static fn() => [
    /**
     * Maximum reserve time for action logs. Default is 3 months.
     * Use PHP DateTime string format.
     */
    'reserve_max_time' => env('ACTION_LOG_MAX_TIME') ?: '3months',

    /**
     * Auto clear chance configuration.
     * Default is 1/100 (1% chance) to run clear on each log creation.)
     */
    'auto_clear' => [
        'chance' => (int) env('ACTION_LOG_CLEAR_CHANCE', '1'),
        'chance_base' => (int) env('ACTION_LOG_CLEAR_CHANCE_BASE', '100'),
    ],

    'hidden_list' => [
        'headers' => [
            'authorization',
        ],
        'body' => [
            'password',
            'secret',
        ],
    ],

    'view' => [
        /**
         * Count total pages in admin list view.
         * Set FALSE to improve performance on large datasets.
         */
        'count_pages' => (bool) env('ACTION_LOG_COUNT_PAGES', '1'),

        /**
         * Display per-page for action log list view.
         */
        'display_limit' => (int) env('ACTION_LOG_DISPLAY_LIMIT', '100'),
    ],

    'providers' => [
        ActionLogPackage::class,
    ],
    'bindings' => [
        //
    ],
];

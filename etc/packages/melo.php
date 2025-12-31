<?php

/**
 * Part of melo project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

use Lyrasoft\Melo\Enum\OrderNoMode;
use Lyrasoft\Melo\MeloPackage;

return [
    'melo' => [
        'providers' => [
            MeloPackage::class,
        ],

        'checkout' => [
            'allow_anonymous' => false,
            'default_expiry' => '+7days',
            'receiver_roles' => ['superuser', 'manager', 'admin'],
        ],

        'order_no' => [
            'mode' => OrderNoMode::INCREMENT_ID,
            'prefix' => 'S',
            'hash_offsets' => 100000,
            'sequence_day_format' => 'Ymd',
            'maxlength' => 20
        ],

        'shop' => [
            'sitename' => 'Melo',
            'logo' => 'vendor/lyrasoft/melo/images/simular-logo.png'
        ]
    ]
];

<?php

/**
 * Part of shopgo project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

use Lyrasoft\ShopGo\Enum\OrderNoMode;

return [
    'shopgo' => [
        'providers' => [
            Lyrasoft\ShopGo\ShopGoPackage::class
        ],

        'currency' => [
            'main' => 'USD'
        ],

        'fixtures' => [
            'locale' => 'en_US',
        ],

        'address' => [
            'use_fullname' => false,
            'use_fulladdress' => false,
        ],

        'order_no' => [
            'mode' => OrderNoMode::INCREMENT_ID(),
            'prefix' => 'S',
            'hash_offsets' => 100000,
            'sequence_day_format' => 'Ymd',
            // Base62
            // If you want to update this, run:
            // `php -r "echo str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');"`
            'hash_seed' => 'E7G5FHBK9NTifV8tZban2ASvQLeRyYwMWqdhDXs61OuPg0Iploc3kUj4rCJmxz'
        ],

        'payment_no' => [
            'maxlength' => 20,
        ],

        'invoice_no' => [
            'prefix' => 'INV'
        ],
    ]
];

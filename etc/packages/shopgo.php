<?php

/**
 * Part of shopgo project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

use Lyrasoft\ShopGo\Ecpay\EcpayPayment;
use Lyrasoft\ShopGo\Ecpay\EcpayShipping;
use Lyrasoft\ShopGo\Enum\OrderNoMode;
use Lyrasoft\ShopGo\Payment\Transfer\TransferPayment;
use Lyrasoft\ShopGo\Shipping\Basic\BasicShipping;
use Lyrasoft\ShopGo\ShopGoPackage;
use Lyrasoft\ShopGo\Subscriber\AdditionalPurchaseSubscriber;
use Lyrasoft\ShopGo\Subscriber\DiscountSubscriber;

return [
    'shopgo' => [
        'providers' => [
            Lyrasoft\ShopGo\ShopGoPackage::class
        ],

        'listeners' => [
            ShopGoPackage::class => [
                AdditionalPurchaseSubscriber::class,
                DiscountSubscriber::class,
            ]
        ],

        'shop' => [
            'sitename' => 'ShopGo',
            'logo' => 'vendor/lyrasoft/shopgo/images/simular-logo.png'
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
            'maxlength' => 20, // Digits length will be maxlength - 9
        ],

        'invoice_no' => [
            'prefix' => 'INV-',
            'length' => 11,
        ],

        'checkout' => [
            'allow_anonymous' => false,
            'partial_checkout' => false,
            'default_expiry' => '+7days',
        ],

        'shipping' => [
            'default_location_id' => env('SHOPGO_DEFAULT_LOCATION_ID'),
            'location_labels' => null,
            'types' => [
                'basic' => BasicShipping::class,
                'ecpay' => EcpayShipping::class,
            ]
        ],

        'payment' => [
            'types' => [
                'transfer' => TransferPayment::class,
                'ecpay' => EcpayPayment::class,
            ]
        ],

        'mpdf' => [
            'font_dirs' => [
                env('SHOPGO_MPDF_FONT_DIR')
            ],
            'font_data' => [],
            'font_family' => ''
        ]
    ]
];

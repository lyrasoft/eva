<?php

declare(strict_types=1);

use Lyrasoft\Contact\ContactPackage;
use Windwalker\Core\Attributes\ConfigModule;

return #[ConfigModule('contact', enabled: true, priority: 100, belongsTo: ContactPackage::class)]
static fn() => [
    'receivers' => [
        'main' => [
            'roles' => [
                'superuser',
                'manager',
                'admin',
            ],
            'cc' => [
                //
            ],
            'bcc' => [
                //
            ]
        ]
    ],

    'rate_limit' => [
        '_default' => [
            'policy' => 'fixed_window',
            'limit' => 10,
            'interval' => '1day',
        ],
        // Add type config
        // 'main' => [
        //     'policy' => 'fixed_window',
        //     'limit' => 10,
        //     'interval' => '1day',
        // ],
    ],

    'providers' => [
        ContactPackage::class
    ],

    'bindings' => [
        //
    ]
];

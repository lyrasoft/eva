<?php

/**
 * Part of eva project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

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

    'providers' => [
        ContactPackage::class
    ],

    'bindings' => [
        //
    ]
];

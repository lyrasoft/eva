<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

return [
    'unicorn' => [
        'package' => \Unicorn\UnicornPackage::class,
        'enabled' => true,

        'listeners' => [
            //
        ],

        'providers' => [
            \Unicorn\UnicornPackage::class
        ]
    ]
];

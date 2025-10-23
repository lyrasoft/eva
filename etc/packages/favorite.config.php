<?php

/**
 * Part of shopgo project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

use Lyrasoft\Favorite\FavoritePackage;
use Windwalker\Core\Attributes\ConfigModule;

return #[ConfigModule('favorite', enabled: true, priority: 100, belongsTo: FavoritePackage::class)]
static fn() => [
    'favorite' => [
        'providers' => [
            FavoritePackage::class,
        ],

        'ajax' => [
            'type_protect' => true,
            'allow_types' => [
                'article',
            ],
        ],
    ],
];

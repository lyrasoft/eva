<?php

declare(strict_types=1);

use Lyrasoft\Favorite\FavoritePackage;
use Windwalker\Core\Attributes\ConfigModule;

return #[ConfigModule('favorite', enabled: true, priority: 100, belongsTo: FavoritePackage::class)]
static fn() => [
    'providers' => [
        FavoritePackage::class,
    ],

    'ajax' => [
        'type_protect' => true,
        'allow_types' => [
            'article',
            'product'
        ],
    ],
];

<?php

/**
 * Part of shopgo project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

use Lyrasoft\Favorite\FavoritePackage;
use Lyrasoft\Luna\Entity\Article;

return [
    'favorite' => [
        'providers' => [
            FavoritePackage::class
        ],

        'ajax' => [
            'type_protect' => false,
            'allow_types' => [
                'article'
            ]
        ],

        'fixtures' => [
            'article' => Article::class,
        ],
    ]
];

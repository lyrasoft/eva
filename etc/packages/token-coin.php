<?php

declare(strict_types=1);

namespace App\Config;

use Lyrasoft\TokenCoin\TokenCoinPackage;

return [
    'token-coin' => [
        'providers' => [
            TokenCoinPackage::class,
        ],

        'callbacks' => [
            'before_save' => [
                // 'type' => function () {}
            ],

            'update_remain' => [
                // 'type' => function () {}
            ],
        ],
    ]
];

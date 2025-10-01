<?php

declare(strict_types=1);

namespace App\Config;

use Lyrasoft\Luna\Entity\User;
use Lyrasoft\TokenCoin\TokenCoinPackage;
use Windwalker\ORM\ORM;

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
                'main' => function (mixed $remain, mixed $targetId, ORM $orm) {
                    $user = $orm->mustFindOne(User::class, $targetId);
                    $params = $user->getParams();
                    $params['token_coins'] = $remain;
                    $user->setParams($params);

                    $orm->updateOne($user);
                }
            ],
        ],
    ]
];

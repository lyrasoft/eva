<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Access\AccessService;
use Lyrasoft\Luna\Entity\User;
use Lyrasoft\TokenCoin\Entity\TokenCoinHistory;
use Lyrasoft\TokenCoin\Enum\TokenCoinAction;
use Lyrasoft\TokenCoin\Service\TokenCoinService;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Crypt\Hasher\PasswordHasherInterface;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * User Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (PasswordHasherInterface $password, AccessService $accessService, TokenCoinService $tokenCoinService) use ($seeder, $orm, $db) {
        $faker = $seeder->faker('zh_TW');

        /** @var EntityMapper<User> $mapper */
        $mapper = $orm->mapper(User::class);

        $pass = $password->hash('1234');
        $basicRoles = $accessService->getBasicRoles();

        foreach (range(1, 50) as $i) {
            $item = $mapper->createEntity();

            $item->setName($faker->name());
            $item->setUsername($faker->userName());
            $item->setEmail($faker->safeEmail());
            $item->setPassword($pass);
            $item->setAvatar($faker->avatar(400));
            $item->setEnabled((bool) $faker->randomElement([1, 1, 1, 0]));
            $item->setVerified(true);
            $item->setLastLogin($faker->dateTimeThisYear());
            $item->setRegistered($faker->dateTimeThisYear());

            $item = $mapper->createOne($item);

            $accessService->addRolesToUser($item, $basicRoles);

            $seeder->outCounting();

            $tokenCoinService->modifyAndAddHistory(
                type: 'main',
                targetId: $item->getId(),
                action: TokenCoinAction::PLUS,
                value: 500,
                // You can modify TokenCoinHistory entity here
                beforeSave: function (TokenCoinHistory $history) use ($item) {
                    $history->setNote('init');
                },
            );
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(User::class);
    }
);

<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\ShopGo\Entity\OrderState;
use Lyrasoft\ShopGo\Entity\Payment;
use Lyrasoft\ShopGo\ShopGoPackage;
use Unicorn\Utilities\SlugHelper;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\Utf8String;

/**
 * Payment Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (ShopGoPackage $shopGo) use ($seeder, $orm, $db) {
        $faker = $seeder->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<Payment> $mapper */
        $mapper = $orm->mapper(Payment::class);

        $handlingStateId = 5;
        $state = $orm->findOne(OrderState::class, $handlingStateId);

        $faker = $faker->unique();

        foreach (range(1, 5) as $i) {
            $item = $mapper->createEntity();

            $item->setTitle(Utf8String::ucfirst($faker->word()) . ' Pay');
            $item->setAlias(SlugHelper::safe($item->getTitle()));
            $item->setDescription($faker->paragraph());
            $item->setImage($faker->unsplashImage(400, 400));
            $item->setOrderStateId($state?->getId() ?? 0);
            $item->setState(1);
            $item->setOrdering($i);

            $mapper->createOne($item);

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Payment::class);
    }
);

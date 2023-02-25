<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\ShopGo\Entity\Shipping;
use Lyrasoft\ShopGo\Shipping\AbstractShipping;
use Lyrasoft\ShopGo\Shipping\ShippingService;
use Lyrasoft\ShopGo\ShopGoPackage;
use Unicorn\Utilities\SlugHelper;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\Utf8String;

/**
 * Shipping Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (ShopGoPackage $shopGo, ShippingService $shippingService) use ($seeder, $orm, $db) {
        $faker = $seeder->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<Shipping> $mapper */
        $mapper = $orm->mapper(Shipping::class);

        $ufaker = $faker->unique();

        $types = $shippingService->getTypes();

        foreach (range(1, 5) as $i) {
            $item = $mapper->createEntity();

            /** @var class-string<AbstractShipping> $type */
            $type = $faker->randomElement($types);

            $item->setTitle(Utf8String::ucfirst($ufaker->word()) . ' Shipping');
            $item->setAlias(SlugHelper::safe($item->getTitle()));
            $item->setSubtitle($faker->sentence());
            $item->setType($type::getType());
            $item->setClassname($type);
            $item->setDescription($faker->paragraph());
            $item->setImage($faker->unsplashImage(400, 400));
            $item->setState(1);
            $item->setOrdering($i + 1);
            $item->setNote($faker->sentence());

            $mapper->createOne($item);

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Shipping::class);
    }
);

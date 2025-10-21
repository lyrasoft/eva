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

            $item->title = Utf8String::ucfirst($ufaker->word()) . ' Shipping';
            $item->alias = SlugHelper::safe($item->title);
            $item->subtitle = $faker->sentence();
            $item->type = $type::getType();
            $item->classname = $type;
            $item->description = $faker->paragraph();
            $item->image = $faker->unsplashImage(400, 400);
            $item->state = 1;
            $item->ordering = $i + 1;
            $item->note = $faker->sentence();

            $instance = $shippingService->createTypeInstance($item);
            $defaultValues = $instance?->getDefaultFormValues();

            $item->pricing = $defaultValues['pricing'] ?? [];

            $item->params = $defaultValues['params'] ?? [];

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

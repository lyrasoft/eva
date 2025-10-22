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
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\Utf8String;

return new /** Shipping Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(ShopGoPackage $shopGo, ShippingService $shippingService): void
    {
        $faker = $this->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<Shipping> $mapper */
        $mapper = $this->orm->mapper(Shipping::class);

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

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Shipping::class);
    }
};

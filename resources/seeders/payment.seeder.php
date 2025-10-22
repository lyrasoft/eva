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
use Lyrasoft\ShopGo\Payment\AbstractPayment;
use Lyrasoft\ShopGo\Payment\PaymentService;
use Lyrasoft\ShopGo\ShopGoPackage;
use Unicorn\Utilities\SlugHelper;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;
use Windwalker\Utilities\Utf8String;

return new /** Payment Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(ShopGoPackage $shopGo, PaymentService $paymentService): void
    {
        $faker = $this->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<Payment> $mapper */
        $mapper = $this->orm->mapper(Payment::class);

        $handlingStateId = 5;
        $state = $this->orm->findOne(OrderState::class, $handlingStateId);

        $ufaker = $faker->unique();

        $types = $paymentService->getTypes();

        foreach (range(1, 5) as $i) {
            $item = $mapper->createEntity();

            /** @var class-string<AbstractPayment> $type */
            $type = $faker->randomElement($types);

            $item->title = Utf8String::ucfirst($ufaker->word()) . ' Pay';
            $item->alias = SlugHelper::safe($item->title);
            $item->subtitle = $faker->sentence();
            $item->type = $type::getType();
            $item->classname = $type;
            $item->description = $faker->paragraph();
            $item->image = $faker->unsplashImage(400, 400);
            $item->orderStateId = $state?->id ?? 0;
            $item->state = 1;
            $item->ordering = $i;
            $item->note = $faker->sentence();

            $instance = $paymentService->createTypeInstance($item);
            $defaultValues = $instance?->getDefaultFormValues();

            $item->params = $defaultValues['params'] ?? [];

            $mapper->createOne($item);

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Payment::class);
    }
};

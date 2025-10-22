<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\ShopGo\Entity\Discount;
use Lyrasoft\ShopGo\Entity\DiscountUsage;
use Lyrasoft\ShopGo\Entity\Product;
use Lyrasoft\ShopGo\Enum\DiscountCombine;
use Lyrasoft\ShopGo\Enum\DiscountMethod;
use Lyrasoft\ShopGo\Enum\DiscountType;
use Lyrasoft\ShopGo\ShopGoPackage;
use Lyrasoft\Luna\Entity\User;
use Lyrasoft\Luna\User\Password;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

use function Windwalker\chronos;

return new /** DiscountSeeder Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(ShopGoPackage $shopGo): void
    {
        $faker = $this->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<Discount> $mapper */
        $mapper = $this->orm->mapper(Discount::class);

        /** @var Product[] $products */
        $products = $this->orm->findList(Product::class)->all()->dump();

        // Products
        foreach ($products as $p => $product) {
            // Special
            $item = $mapper->createEntity();

            $item->type = DiscountType::PRODUCT;
            $item->subtype = 'special';
            $item->productId = $product->id;
            $item->publishUp = chronos('-3months');
            $item->publishDown = chronos('+3months');
            $item->method = $faker->randomElement(DiscountMethod::cases());
            $item->price = match ($item->method->getValue()) {
                DiscountMethod::OFFSETS => - (random_int(1, 20) * 10),
                DiscountMethod::FIXED => random_int(5, 40) * 100,
                DiscountMethod::PERCENTAGE => random_int(1, 9) * 10,
                default => 0
            };
            $item->state = 1;
            $item->ordering = 1;

            $mapper->createOne($item);
            $this->printCounting();

            // Discount
            if ($p + 1 === count($products)) {
                foreach (range(1, 3) as $d) {
                    $item = $mapper->createEntity();

                    $item->type = DiscountType::PRODUCT;
                    $item->subtype = 'discount';
                    $item->productId = $product->id;
                    $item->minProductQuantity = $d * 3;
                    $item->price = -(random_int(1, 20) * 10);
                    $item->method = DiscountMethod::OFFSETS;
                    $item->state = 1;
                    $item->ordering = $d + 1;

                    $mapper->createOne($item);
                    $this->printCounting();
                }
            }
        }

        // Global
        foreach (range(1, 10) as $g) {
            $item = $mapper->createEntity();

            /** @var "basic"|"code" $subType */
            $subType = $faker->randomElement(DiscountType::GLOBAL->getSubTypes());

            $item->type = DiscountType::GLOBAL;
            $item->subtype = $subType;
            $item->title = $faker->sentence(2);
            $item->method = $faker->randomElement(DiscountMethod::cases());
            $item->price = match ($item->method->getValue()) {
                DiscountMethod::OFFSETS => - (random_int(1, 20) * 10),
                DiscountMethod::FIXED => random_int(5, 40) * 100,
                DiscountMethod::PERCENTAGE => random_int(1, 9) * 10,
                default => 0,
            };
            $item->freeShipping = $faker->randomElement([true, false]);
            $item->publishUp = $faker->dateTimeThisYear();
            $item->publishDown = $item->publishUp->modify('+60days');
            $item->description = $faker->paragraph();

            if ($subType === 'code') {
                $item->code = Password::genRandomPassword(10);
            }

            $item->minPrice = random_int(0, 50) * 100;
            $item->quantity = random_int(50, 5000);
            $item->timesPerUser = random_int(0, 10) ?: null;
            $item->firstBuy = random_int(0, 3) ?: null;
            $item->minCartItems = random_int(0, 5) ?: null;
            $item->combine = $faker->randomElement(DiscountCombine::cases());
            $item->state = 1;
            $item->ordering = $g;

            $mapper->createOne($item);

            $this->printCounting();
        }

        // Coupons
        $users = $this->orm->from(User::class)
            ->where('id', '<', 20)
            ->all(User::class)
            ->dump();

        foreach ($users as $u => $user) {
            $item = $mapper->createEntity();

            /** @var "auto" $subType */
            $subType = $faker->randomElement(DiscountType::COUPON->getSubTypes());

            $item->type = DiscountType::COUPON;
            $item->subtype = $subType;
            $item->title = $faker->sentence(2);
            $item->method = $faker->randomElement(DiscountMethod::cases());
            $item->price = match ($item->method->getValue()) {
                DiscountMethod::OFFSETS => - (random_int(1, 20) * 10),
                DiscountMethod::FIXED => random_int(5, 40) * 100,
                DiscountMethod::PERCENTAGE => random_int(1, 9) * 10,
                default => 0,
            };
            $item->freeShipping = $faker->randomElement([true, false]);
            $item->publishUp = $faker->dateTimeThisYear();
            $item->publishDown = $item->publishUp->modify('+60days');
            $item->description = $faker->paragraph();
            $item->code = Password::genRandomPassword(10);
            $item->minPrice = random_int(0, 50) * 100;
            $item->quantity = random_int(50, 5000);
            $item->timesPerUser = random_int(0, 10) ?: null;
            $item->firstBuy = random_int(0, 3) ?: null;
            $item->minCartItems = random_int(0, 5) ?: null;
            $item->combine = $faker->randomElement(DiscountCombine::cases());
            $item->state = 1;
            $item->ordering = $g;

            $mapper->createOne($item);

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Discount::class, DiscountUsage::class);
    }
};

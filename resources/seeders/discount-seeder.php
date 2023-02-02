<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

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
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

use function Windwalker\chronos;

/**
 * DiscountSeeder Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (ShopGoPackage $shopGo) use ($seeder, $orm, $db) {
        $faker = $seeder->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<Discount> $mapper */
        $mapper = $orm->mapper(Discount::class);

        /** @var Product[] $products */
        $products = $orm->findList(Product::class)->all()->dump();

        // Products
        foreach ($products as $p => $product) {
            // Special
            $item = $mapper->createEntity();

            $item->setType(DiscountType::PRODUCT());
            $item->setSubtype('special');
            $item->setProductId($product->getId());
            $item->setPublishUp(chronos('-6months'));
            $item->setPublishDown(chronos('+6months'));
            $item->setMethod($faker->randomElement(DiscountMethod::cases()));
            $item->setPrice(match ($item->getMethod()->getValue()) {
                DiscountMethod::OFFSETS => - (random_int(1, 10) * 100),
                DiscountMethod::FIXED => random_int(5, 40) * 100,
                DiscountMethod::PERCENTAGE => random_int(1, 10) / 10,
            });
            $item->setState(1);
            $item->setOrdering(1);

            $mapper->createOne($item);
            $seeder->outCounting();

            // Discount
            if ($p + 1 === count($products)) {
                foreach (range(1, 3) as $d) {
                    $item = $mapper->createEntity();

                    $item->setType(DiscountType::PRODUCT());
                    $item->setSubtype('discount');
                    $item->setProductId($product->getId());
                    $item->setMinProductQuantity($d * 3);
                    $item->setPrice(-(random_int(1, 5) * 100));
                    $item->setMethod(DiscountMethod::OFFSETS());
                    $item->setState(1);
                    $item->setOrdering($d + 1);

                    $mapper->createOne($item);
                    $seeder->outCounting();
                }
            }
        }

        // Global
        foreach (range(1, 10) as $g) {
            $item = $mapper->createEntity();

            /** @var "basic"|"code" $subType */
            $subType = $faker->randomElement(DiscountType::GLOBAL()->getSubTypes());

            $item->setType(DiscountType::GLOBAL());
            $item->setSubtype($subType);
            $item->setTitle($faker->sentence(2));
            $item->setMethod($faker->randomElement(DiscountMethod::cases()));
            $item->setPrice(match ($item->getMethod()->getValue()) {
                DiscountMethod::OFFSETS => - (random_int(1, 10) * 100),
                DiscountMethod::FIXED => random_int(5, 40) * 100,
                DiscountMethod::PERCENTAGE => random_int(1, 10) / 10,
            });
            $item->setFreeShipping($faker->randomElement([true, false]));
            $item->setPublishUp($faker->dateTimeThisYear());
            $item->setPublishDown(
                $item->getPublishUp()->modify('+60days')
            );
            $item->setDescription($faker->paragraph());

            if ($subType === 'code') {
                $item->setCode(Password::genRandomPassword(10));
            }

            $item->setMinPrice(random_int(0, 50) * 100);
            $item->setQuantity(random_int(50, 5000));
            $item->setTimesPerUser(random_int(0, 10) ?: null);
            $item->setFirstBuy(random_int(0, 3) ?: null);
            $item->setMinCartItems(random_int(0, 5) ?: null);
            $item->setCombine($faker->randomElement(DiscountCombine::cases()));
            $item->setState(1);
            $item->setOrdering($g);

            $mapper->createOne($item);

            $seeder->outCounting();
        }

        // Coupons
        $users = $orm->from(User::class)
            ->where('id', '<', 20)
            ->all(User::class)
            ->dump();

        foreach ($users as $u => $user) {
            $item = $mapper->createEntity();

            /** @var "auto" $subType */
            $subType = $faker->randomElement(DiscountType::COUPON()->getSubTypes());

            $item->setType(DiscountType::COUPON());
            $item->setSubtype($subType);
            $item->setTitle($faker->sentence(2));
            $item->setMethod($faker->randomElement(DiscountMethod::cases()));
            $item->setPrice(match ($item->getMethod()->getValue()) {
                DiscountMethod::OFFSETS => - (random_int(1, 10) * 100),
                DiscountMethod::FIXED => random_int(5, 40) * 100,
                DiscountMethod::PERCENTAGE => random_int(1, 10) / 10,
            });
            $item->setFreeShipping($faker->randomElement([true, false]));
            $item->setPublishUp($faker->dateTimeThisYear());
            $item->setPublishDown(
                $item->getPublishUp()->modify('+60days')
            );
            $item->setDescription($faker->paragraph());
            $item->setCode(Password::genRandomPassword(10));
            $item->setMinPrice(random_int(0, 50) * 100);
            $item->setQuantity(random_int(50, 5000));
            $item->setTimesPerUser(random_int(0, 10) ?: null);
            $item->setFirstBuy(random_int(0, 3) ?: null);
            $item->setMinCartItems(random_int(0, 5) ?: null);
            $item->setCombine($faker->randomElement(DiscountCombine::cases()));
            $item->setState(1);
            $item->setOrdering($g);

            $mapper->createOne($item);

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Discount::class, DiscountUsage::class);
    }
);

<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\ShopGo\Entity\AdditionalPurchase;
use Lyrasoft\ShopGo\Entity\AdditionalPurchaseMap;
use Lyrasoft\ShopGo\Entity\Product;
use Lyrasoft\ShopGo\Entity\ProductVariant;
use Lyrasoft\ShopGo\ShopGoPackage;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Data\Collection;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Admin/additionalPurchase Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (ShopGoPackage $shopGo) use ($seeder, $orm, $db) {
        $faker = $seeder->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<AdditionalPurchase> $mapper */
        $mapper = $orm->mapper(AdditionalPurchase::class);

        /** @var Collection<Product> $products */
        $products = $orm->findList(Product::class)->all();

        /** @var Collection<Product> $attachmentProducts */
        /** @var Collection<Product> $primaryProducts */
        [$attachmentProducts, $primaryProducts] = $products->partition(
            fn (Product $product) => $product->canAttach()
        );

        /** @var ProductVariant[][] $variantGroups */
        $variantGroups = $orm->findList(ProductVariant::class)->all()->groupBy('productId');

        $i = 1;

        foreach ($attachmentProducts as $attachmentProduct) {
            /** @var Collection<ProductVariant> $variants */
            $variants = $variantGroups[$attachmentProduct->getId()];

            /** @var ProductVariant[] $chosenVariants */
            $chosenVariants = $faker->randomElements($variants->dump(), random_int(1, count($variants)));

            foreach ($chosenVariants as $variant) {
                $item = $mapper->createEntity();

                $item->setTitle(
                    trim($attachmentProduct->getTitle() . ' - ' . $variant->getTitle(), ' -')
                );
                $item->setAttachProductId($attachmentProduct->getId());
                $item->setAttachVariantId($variant->getId());
                $item->setPrice(random_int(1, 30) * 100);
                $item->setState(1);
                $item->setOrdering($i);

                $ap = $mapper->createOne($item);

                $i++;

                /** @var Product[] $chosenPrimaryProducts */
                $chosenPrimaryProducts = $faker->randomElements($primaryProducts->dump(), random_int(1, 5));

                foreach ($chosenPrimaryProducts as $chosenPrimaryProduct) {
                    $map = new AdditionalPurchaseMap();

                    $map->setAdditionalPurchaseId($ap->getId());
                    $map->setAttachProductId($attachmentProduct->getId());
                    $map->setAttachVariantId($variant->getId());
                    $map->setTargetProductId($chosenPrimaryProduct->getId());

                    $orm->createOne(AdditionalPurchaseMap::class, $map);

                    $seeder->outCounting();
                }

            }
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        //
    }
);

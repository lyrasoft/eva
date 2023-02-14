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
use Lyrasoft\ShopGo\Entity\AdditionalPurchaseAttachment;
use Lyrasoft\ShopGo\Entity\AdditionalPurchaseTarget;
use Lyrasoft\ShopGo\Entity\Product;
use Lyrasoft\ShopGo\Entity\ProductVariant;
use Lyrasoft\ShopGo\Enum\DiscountMethod;
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
        /** @var Collection<Product> $targetProducts */
        [$attachmentProducts, $targetProducts] = $products->partition(
            fn (Product $product) => $product->canAttach()
        );

        /** @var ProductVariant[][] $variantGroups */
        $variantGroups = $orm->findList(ProductVariant::class)->all()->groupBy('productId');

        $i = 1;

        foreach ($attachmentProducts as $a => $attachmentProduct) {
            $item = $mapper->createEntity();

            $item->setTitle($attachmentProduct->getTitle());
            $item->setState(1);
            $item->setOrdering($a);

            $ap = $mapper->createOne($item);

            /** @var Collection<ProductVariant> $variants */
            $variants = $variantGroups[$attachmentProduct->getId()];

            /** @var ProductVariant[] $chosenVariants */
            $chosenVariants = $faker->randomElements($variants->dump(), random_int(1, count($variants)));

            foreach ($chosenVariants as $variant) {
                $attachment = new AdditionalPurchaseAttachment();

                $attachment->setAdditionalPurchaseId($ap->getId());
                $attachment->setProductId($attachmentProduct->getId());
                $attachment->setVariantId($variant->getId());
                $attachment->setMethod(DiscountMethod::OFFSETS());
                $attachment->setPrice(-200);
                $attachment->setMaxQuantity(random_int(3, 7));
                $attachment->setState(1);
                $attachment->setOrdering($i);

                $attachment = $orm->createOne(AdditionalPurchaseAttachment::class, $attachment);

                $i++;

                $seeder->outCounting();
            }

            /** @var Product[] $chosenTargetProducts */
            $chosenTargetProducts = $faker->randomElements($targetProducts->dump(), random_int(1, 5));

            foreach ($chosenTargetProducts as $chosenTargetProduct) {
                $map = new AdditionalPurchaseTarget();

                $map->setAdditionalPurchaseId($ap->getId());
                $map->setProductId($chosenTargetProduct->getId());

                $orm->createOne(AdditionalPurchaseTarget::class, $map);

                $seeder->outCounting();
            }
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        //
    }
);

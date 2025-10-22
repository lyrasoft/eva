<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\ShopGo\Entity\AdditionalPurchase;
use Lyrasoft\ShopGo\Entity\AdditionalPurchaseAttachment;
use Lyrasoft\ShopGo\Entity\AdditionalPurchaseTarget;
use Lyrasoft\ShopGo\Entity\Product;
use Lyrasoft\ShopGo\Entity\ProductVariant;
use Lyrasoft\ShopGo\Enum\DiscountMethod;
use Lyrasoft\ShopGo\ShopGoPackage;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\Data\Collection;
use Windwalker\ORM\EntityMapper;

return new /** AdditionalPurchase Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(ShopGoPackage $shopGo): void
    {
        // Use package-configured locale if available.
        $faker = $this->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<AdditionalPurchase> $mapper */
        $mapper = $this->orm->mapper(AdditionalPurchase::class);

        /** @var Collection<Product> $products */
        $products = $this->orm->findList(Product::class)->all();

        /** @var Collection<Product> $attachmentProducts */
        /** @var Collection<Product> $targetProducts */
        [$attachmentProducts, $targetProducts] = $products->partition(
            fn (Product $product) => $product->canAttach
        );

        /** @var ProductVariant[][] $variantGroups */
        $variantGroups = $this->orm->findList(ProductVariant::class)->all()->groupBy('productId');

        $i = 1;

        foreach ($attachmentProducts as $a => $attachmentProduct) {
            $item = $mapper->createEntity();

            $item->title = $attachmentProduct->title;
            $item->state = 1;
            $item->ordering = $a;

            $ap = $mapper->createOne($item);

            /** @var Collection<ProductVariant> $variants */
            $variants = $variantGroups[$attachmentProduct->id];

            /** @var ProductVariant[] $chosenVariants */
            $chosenVariants = $faker->randomElements($variants->dump(), random_int(1, count($variants)));

            foreach ($chosenVariants as $variant) {
                $attachment = new AdditionalPurchaseAttachment();

                $attachment->additionalPurchaseId = $ap->id;
                $attachment->productId = $attachmentProduct->id;
                $attachment->variantId = $variant->id;
                $attachment->method = DiscountMethod::OFFSETS;
                $attachment->price = -200;
                $attachment->maxQuantity = random_int(3, 7);
                $attachment->state = 1;
                $attachment->ordering = $i;

                $attachment = $this->orm->createOne(AdditionalPurchaseAttachment::class, $attachment);

                $i++;

                $this->printCounting();
            }

            /** @var Product[] $chosenTargetProducts */
            $chosenTargetProducts = $faker->randomElements($targetProducts->dump(), random_int(1, 5));

            foreach ($chosenTargetProducts as $chosenTargetProduct) {
                $map = new AdditionalPurchaseTarget();

                $map->additionalPurchaseId = $ap->id;
                $map->productId = $chosenTargetProduct->id;

                $this->orm->createOne(AdditionalPurchaseTarget::class, $map);

                $this->printCounting();
            }
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        // No-op clear. If needed, use $this->db or $this->orm here.
    }
};

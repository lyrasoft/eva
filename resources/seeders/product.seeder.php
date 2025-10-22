<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\ShopGo\Data\ListOption;
use Lyrasoft\ShopGo\Data\ListOptionCollection;
use Lyrasoft\ShopGo\Entity\Product;
use Lyrasoft\ShopGo\Entity\ProductAttribute;
use Lyrasoft\ShopGo\Entity\ProductAttributeMap;
use Lyrasoft\ShopGo\Entity\ProductFeature;
use Lyrasoft\ShopGo\Entity\ProductVariant;
use Lyrasoft\ShopGo\Entity\ShopCategoryMap;
use Lyrasoft\ShopGo\Enum\ProductAttributeType;
use Lyrasoft\ShopGo\Service\ProductAttributeService;
use Lyrasoft\ShopGo\Service\VariantService;
use Lyrasoft\ShopGo\ShopGoPackage;
use Unicorn\Utilities\SlugHelper;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Data\Collection;
use Windwalker\ORM\EntityMapper;
use Windwalker\Utilities\Str;
use Windwalker\Utilities\Utf8String;

use function Windwalker\chronos;
use function Windwalker\filter;
use function Windwalker\uid;

return new /** Product Seeder */ class extends AbstractSeeder {
    #[\Windwalker\Core\Seed\SeedImport]
    public function import(ShopGoPackage $shopGo, ProductAttributeService $productAttributeService): void
    {
        $faker = $this->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<Product> $mapper */
        $mapper = $this->orm->mapper(Product::class);

        /** @var EntityMapper<ShopCategoryMap> $mapMapper */
        $mapMapper = $this->orm->mapper(ShopCategoryMap::class);

        $categoryIds = $this->orm->findColumn(Category::class, 'id', ['type' => 'product'])->dump();

        $features = $this->orm->findList(ProductFeature::class)->all();

        foreach (range(1, 100) as $i) {
            $haveStartDay = $faker->randomElement([1, 0, 0, 0]);
            $item = $mapper->createEntity();

            $startDay = chronos($faker->randomElement(['-5 days', '-10 days', '-15 days', '-50days']));

            $item->categoryId = (int) $faker->randomElement($categoryIds);
            // $item->setPrimaryVariantId();
            $item->model = 'PD-' . Str::padLeft((string) $i, 7, '0');
            $item->title = Utf8String::ucwords(
                $faker->sentence(1)
            );
            $item->alias = SlugHelper::safe($item->title);
            $item->originPrice = (float) $faker->randomElement([500, 1000, 1500, 2000, 2500, 3000, 3500]);
            $item->safeStock = random_int(3, 5);
            $item->intro = $faker->paragraph(2);
            $item->description = $faker->paragraph(5);
            $item->meta = [
                'title' => $item->title,
                'description' => $item->description,
                'keywords' => implode(',', $faker->words()),
            ];
            $item->canAttach = (bool) $faker->optional(0.1, 0)->passthrough(1);
            // $item->setVariants();
            $item->ordering = (int) $i;
            // $item->setHide();
            $item->state = $faker->optional(0.7, 0)->passthrough(1);
            // $item->setSearchIndex();
            // $item->setShippings();
            $item->created = $faker->dateTimeThisYear();
            $item->modified = $item->created->modify('+10days');
            $item->createdBy = 1;
            $item->hits = random_int(1, 9999);
            $item->params = [];

            if ($haveStartDay === 1) {
                $item->publishUp = $startDay;
                $item->publishDown = $startDay->modify('+25 days');
            }

            $item = $mapper->createOne($item);

            $categoryIds = array_filter(
                $categoryIds,
                static fn($v) => $v !== $item->categoryId
            );

            // Primary category
            $map = $mapMapper->createEntity();

            $map->type = 'product';
            $map->targetId = $item->id;
            $map->categoryId = $item->categoryId;
            $map->primary = true;
            $map->ordering = 1;

            $mapMapper->createOne($map);

            // Sub categories
            foreach ($faker->randomElements($categoryIds, 3) as $k => $categoryId) {
                $map = $mapMapper->createEntity();

                $map->type = 'product';
                $map->targetId = $item->id;
                $map->categoryId = (int) $categoryId;
                $map->ordering = $k + 2;

                $mapMapper->createOne($map);
            }

            // Attributes
            [$attributes] = $productAttributeService->getAttributesAndGroupsByCategoryId($item->categoryId);

            /** @var ProductAttribute[] $attributes */
            foreach ($attributes as $attribute) {
                $attrMap = new ProductAttributeMap();
                $attrMap->attributeId = $attribute->id;
                $attrMap->key = $attribute->key;
                $attrMap->productId = $item->id;
                $attrMap->locale = '*';

                if ($attribute->type === ProductAttributeType::BOOL) {
                    $attrMap->value = (string) random_int(0, 1);
                } elseif ($attribute->type === ProductAttributeType::TEXT) {
                    $attrMap->value = $faker->sentence();
                } elseif ($attribute->type === ProductAttributeType::SELECT) {
                    $options = $attribute->options->dump();
                    $option = $faker->randomElement($options);
                    $attrMap->value = $option->value;
                }

                $this->orm->createOne(ProductAttributeMap::class, $attrMap);
            }

            // Main Variant
            $variant = new ProductVariant();
            $variant->productId = $item->id;
            $variant->title = $item->title;
            $variant->hash = '';
            $variant->primary = true;
            $variant->sku = 'PRD' . Str::padLeft((string) $i, 7, '0');
            $variant->stockQuantity = random_int(1, 30);
            $variant->subtract = true;
            $variant->price = random_int(1, 40) * 100;
            $variant->dimension
                ->setWidth(random_int(20, 100))
                ->setHeight(random_int(20, 100))
                ->setLength(random_int(20, 100))
                ->setWeight(random_int(20, 100));
            $variant->outOfStockText = '';
            $variant->cover = $faker->unsplashImage(800, 800);
            $variant->images = array_map(
                static fn($image) => [
                    'url' => $image,
                    'uid' => uid(),
                ],
                $faker->unsplashImages(5, 800, 800)
            );
            $variant->state = 1;

            $searchIndexes = [];

            $mainVariant = $this->orm->createOne(ProductVariant::class, $variant);

            $searchIndexes[] = $mainVariant->searchIndex;

            // Sub Variants
            $currentFeatures = [];

            foreach ($faker->randomElements($features, 3) as $feature) {
                /** @var ProductFeature $feature */
                $feature = clone $feature;

                /** @var ListOption[] $options */
                $options = $faker->randomElements($feature->options->dump(), 3);

                foreach ($options as $option) {
                    $option->setParentId($feature->id);
                }

                $feature->options = $options;

                $currentFeatures[] = $feature;
            }

            $hasSubVariants = $faker->randomElement([true, true, false]);

            /** @var array<ListOption[]> $variantGroups */
            $variantGroups = $hasSubVariants ? $this->sortGroup($currentFeatures) : [];

            foreach ($variantGroups as $h => $options) {
                $options = ListOptionCollection::wrap($options);
                $variant = new ProductVariant();

                $optUids = ListOptionCollection::wrap($options)
                    ->as(Collection::class)
                    ->map(static fn($option) => $option['uid'])
                    ->dump();

                $variant->productId = $item->id;
                $variant->title = (string) $options->as(Collection::class)->column('text')->implode(' / ');
                $variant->hash = VariantService::hash($optUids, $seed);
                $variant->primary = false;
                $variant->sku = 'PRD' . Str::padLeft((string) $i, 7, '0') . '-' . ($h + 1);
                $variant->stockQuantity = random_int(1, 30);
                $variant->subtract = true;
                $variant->price = filter(
                    $mainVariant->price + (random_int(-10, 10) * 100),
                    'range(min: 0, max: 100)'
                );
                $variant->dimension
                    ->setWidth(random_int(20, 100))
                    ->setHeight(random_int(20, 100))
                    ->setLength(random_int(20, 100))
                    ->setWeight(random_int(20, 100));
                $variant->outOfStockText = '';
                $variant->cover = $faker->unsplashImage(800, 800);
                $variant->images = array_map(
                    static fn($image) => [
                        'url' => $image,
                        'uid' => uid(),
                    ],
                    $faker->unsplashImages(3, 800, 800)
                );
                $variant->options = $options;
                $variant->state = 1;

                $variant->params = compact('seed');

                $this->orm->createOne(ProductVariant::class, $variant);

                $searchIndexes[] = $variant->getSearchIndex();

                $this->printCounting();
            }

            $mapper->updateWhere(
                [
                    'variants' => count($variantGroups),
                    'primary_variant_id' => $mainVariant->id,
                    'search_index' => implode('|', array_filter($searchIndexes)),
                ],
                ['id' => $item->id]
            );
        }
    }

    /**
     * @param  array<ProductFeature>  $features
     * @param  array<ListOption>  $parentGroup
     *
     * @return  array
     */
    protected function sortGroup(array $features, array $parentGroup = []): array
    {
        $feature = array_pop($features);

        if (!$feature) {
            return [];
        }

        $currentOptions = $feature->options;

        $returnValue = [];

        foreach ($currentOptions as $option) {
            $group = $parentGroup;
            $option['parentId'] = $feature->id;

            $group[] = new ListOption($option);

            if (count($features)) {
                $returnValue[] = $this->sortGroup($features, $group);
            } else {
                $returnValue[] = [$group];
            }
        }

        return array_merge(...$returnValue);
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Product::class, ProductVariant::class);
        $this->truncate(ShopCategoryMap::class);
    }
};

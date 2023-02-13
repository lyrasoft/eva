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
use Windwalker\Core\Seed\Seeder;
use Windwalker\Data\Collection;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\Str;
use Windwalker\Utilities\Utf8String;

use function Windwalker\uid;

/**
 * Product Seeder
 *
 * @var Seeder $seeder
 * @var ORM $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (
        ShopGoPackage $shopGo,
        ProductAttributeService $productAttributeService
    ) use (
        $seeder,
        $orm,
        $db,
        &
        $sortGroup
    ) {
        $faker = $seeder->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<Product> $mapper */
        $mapper = $orm->mapper(Product::class);

        /** @var EntityMapper<ShopCategoryMap> $mapMapper */
        $mapMapper = $orm->mapper(ShopCategoryMap::class);

        $categoryIds = $orm->findColumn(Category::class, 'id', ['type' => 'product'])->dump();

        $features = $orm->findList(ProductFeature::class)->all();

        foreach (range(1, 100) as $i) {
            $item = $mapper->createEntity();

            $item->setCategoryId((int) $faker->randomElement($categoryIds));
            // $item->setPrimaryVariantId();
            $item->setModel('PD-' . Str::padLeft((string) $i, 7, '0'));
            $item->setTitle(
                Utf8String::ucwords(
                    $faker->sentence(1)
                )
            );
            $item->setAlias(SlugHelper::safe($item->getTitle()));
            $item->setOriginPrice((float) $faker->randomElement([500, 1000, 1500, 2000, 2500, 3000, 3500]));
            $item->setSafeStock(random_int(3, 5));
            $item->setIntro($faker->paragraph(2));
            $item->setDescription($faker->paragraph(5));
            $item->setMeta(
                [
                    'title' => $item->getTitle(),
                    'description' => $item->getDescription(),
                    'keywords' => implode(',', $faker->words()),
                ]
            );
            $item->setCanAttach((bool) $faker->optional(0.1, 0)->passthrough(1));
            // $item->setVariants();
            $item->setOrdering((int) $i);
            // $item->setHide();
            $item->setState($faker->optional(0.7, 0)->passthrough(1));
            // $item->setSearchIndex();
            // $item->setShippings();
            $item->setCreated($faker->dateTimeThisYear());
            $item->setModified($item->getCreated()->modify('+10days'));
            $item->setCreatedBy(1);
            $item->setHits(random_int(1, 9999));
            $item->setParams([]);

            $item = $mapper->createOne($item);

            $catelogIds = array_filter(
                $categoryIds,
                static fn($v) => $v !== $item->getCategoryId()
            );

            // Primary category
            $map = $mapMapper->createEntity();

            $map->setType('product');
            $map->setTargetId($item->getId());
            $map->setCategoryId($item->getCategoryId());
            $map->setPrimary(true);
            $map->setOrdering(1);

            $mapMapper->createOne($map);

            // Sub categories
            foreach ($faker->randomElements($catelogIds, 3) as $k => $catelogId) {
                $map = $mapMapper->createEntity();

                $map->setType('product');
                $map->setTargetId($item->getId());
                $map->setCategoryId((int) $catelogId);
                $map->setOrdering($k + 2);

                $mapMapper->createOne($map);
            }

            // Attributes
            [$attributes] = $productAttributeService->getAttributesAndGroupsByCategoryId($item->getCategoryId());

            /** @var ProductAttribute[] $attributes */
            foreach ($attributes as $attribute) {
                $attrMap = new ProductAttributeMap();
                $attrMap->setAttributeId($attribute->getId());
                $attrMap->setKey($attribute->getKey());
                $attrMap->setProductId($item->getId());
                $attrMap->setLocale('*');

                if ($attribute->getType() === ProductAttributeType::BOOL()) {
                     $attrMap->setValue((string) random_int(0, 1));
                } elseif ($attribute->getType() === ProductAttributeType::TEXT()) {
                    $attrMap->setValue($faker->sentence());
                } elseif ($attribute->getType() === ProductAttributeType::SELECT()) {
                    $options = $attribute->getOptions()->dump();
                    $option = $faker->randomElement($options);
                    $attrMap->setValue($option->value);
                }

                $orm->createOne(ProductAttributeMap::class, $attrMap);
            }

            // Main Variant
            $variant = new ProductVariant();
            $variant->setProductId($item->getId());
            $variant->setTitle($item->getTitle());
            $variant->setHash('');
            $variant->setPrimary(true);
            $variant->setSku('PRD' . Str::padLeft((string) $i, 7, '0'));
            $variant->setStockQuantity(random_int(1, 30));
            $variant->setSubtract(true);
            $variant->setPrice(random_int(1, 40) * 100);
            $variant->getDimension()
                ->setWidth(random_int(20, 100))
                ->setHeight(random_int(20, 100))
                ->setLength(random_int(20, 100))
                ->setWeight(random_int(20, 100));
            $variant->setOutOfStockText('');
            $variant->setCover($faker->unsplashImage(800, 800));
            $variant->setImages(
                array_map(
                    static fn($image) => [
                        'url' => $image,
                        'uid' => uid(),
                    ],
                    $faker->unsplashImages(5, 800, 800)
                )
            );
            $variant->setState(1);

            $searchIndexes = [];

            $mainVariant = $orm->createOne(ProductVariant::class, $variant);

            $searchIndexes[] = $mainVariant->getSearchIndex();

            // Sub Variants
            $currentFeatures = [];

            foreach ($faker->randomElements($features, 3) as $feature) {
                /** @var ProductFeature $feature */
                $feature = clone $feature;

                /** @var ListOption[] $options */
                $options = $faker->randomElements($feature->getOptions()->dump(), 3);

                foreach ($options as $option) {
                    $option->setParentId($feature->getId());
                }

                $feature->setOptions($options);

                $currentFeatures[] = $feature;
            }

            $hasSubVariants = $faker->randomElement([true, true, false]);

            /** @var array<ListOption[]> $variantGroups */
            $variantGroups = $hasSubVariants ? $sortGroup($currentFeatures) : [];

            foreach ($variantGroups as $h => $options) {
                $options = ListOptionCollection::wrap($options);
                $variant = new ProductVariant();
                $startDay = $item->getCreated()->modify($faker->randomElement(['+5 days', '+10 days', '+15 days']));
                $haveStartDay = $faker->randomElement([1, 1, 0]);

                $optUids = ListOptionCollection::wrap($options)
                    ->as(Collection::class)
                    ->map(static fn ($option) => $option['uid'])
                    ->dump();

                $variant->setProductId($item->getId());
                $variant->setTitle((string) $options->as(Collection::class)->column('text')->implode(' / '));
                $variant->setHash(VariantService::hash($optUids, $seed));
                $variant->setPrimary(false);
                $variant->setSku('PRD' . Str::padLeft((string) $i, 7, '0') . '-' . ($h + 1));
                $variant->setStockQuantity(random_int(1, 30));
                $variant->setSubtract(true);
                $variant->setPrice($mainVariant->getPrice() + (random_int(-10, 10) * 100));
                $variant->getDimension()
                    ->setWidth(random_int(20, 100))
                    ->setHeight(random_int(20, 100))
                    ->setLength(random_int(20, 100))
                    ->setWeight(random_int(20, 100));
                $variant->setOutOfStockText('');
                $variant->setCover($faker->unsplashImage(800, 800));
                $variant->setImages(
                    array_map(
                        static fn($image) => [
                            'url' => $image,
                            'uid' => uid(),
                        ],
                        $faker->unsplashImages(3, 800, 800)
                    )
                );
                $variant->setOptions($options);
                $variant->setState(1);

                if ($haveStartDay === 1) {
                    $variant->setPublishUp($startDay);
                    $variant->setPublishDown($startDay->modify('+25 days'));
                }

                $variant->setParams(compact('seed'));

                $orm->createOne(ProductVariant::class, $variant);

                $searchIndexes[] = $variant->getSearchIndex();

                $seeder->outCounting();
            }

            $mapper->updateWhere(
                [
                    'variants' => count($variantGroups),
                    'primary_variant_id' => $mainVariant->getId(),
                    'search_index' => implode('|', array_filter($searchIndexes)),
                ],
                ['id' => $item->getId()]
            );
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Product::class, ProductVariant::class);
        $seeder->truncate(ShopCategoryMap::class);
    }
);

/**
 * @param  array<ProductFeature>  $features
 * @param  array<ProductFeature>  $parentGroup
 *
 * @return  array<ListOption>
 */
$sortGroup = static function (array $features, array $parentGroup = []) use (&$sortGroup, $seeder) {
    $feature = array_pop($features);

    if (!$feature) {
        return [];
    }

    $currentOptions = $feature->getOptions();

    $returnValue = [];

    foreach ($currentOptions as $option) {
        $group = $parentGroup;
        $option['parentId'] = $feature->getId();

        $group[] = new ListOption($option);

        if (count($features)) {
            $returnValue[] = $sortGroup($features, $group);
        } else {
            $returnValue[] = [$group];
        }
    }

    return array_merge(...$returnValue);
};

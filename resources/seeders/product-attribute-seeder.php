<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\ShopGo\Data\ListOption;
use Lyrasoft\ShopGo\Entity\ProductAttribute;
use Lyrasoft\ShopGo\Entity\ShopCategoryMap;
use Lyrasoft\ShopGo\Enum\ProductAttributeType;
use Lyrasoft\ShopGo\ShopGoPackage;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\StrNormalize;

use function Windwalker\tid;

/**
 * ProductAttribute Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (ShopGoPackage $shopGo) use ($seeder, $orm, $db) {
        $faker = $seeder->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<ProductAttribute> $mapper */
        $mapper = $orm->mapper(ProductAttribute::class);
        $productCategoryIds = $orm->findColumn(Category::class, 'id', ['type' => 'product'])->dump();
        $groupIds = $orm->findColumn(Category::class, 'id', ['type' => 'attribute'])->dump();

        // Make Group Maps
        foreach ($groupIds as $groupId) {
            foreach ($faker->randomElements($productCategoryIds, 15) as $productCategoryId) {
                $map = new ShopCategoryMap();
                $map->setType('attribute_group');
                $map->setCategoryId((int) $productCategoryId);
                $map->setTargetId((int) $groupId);

                $orm->createOne(ShopCategoryMap::class, $map);
            }
        }

        $groupIds[] = '0';

        foreach (range(1, 30) as $i) {
            $type = $faker->randomElement(ProductAttributeType::values());
            $groupId = $faker->randomElement($groupIds);

            $item = $mapper->createEntity();
            $item->setType($type);
            $item->setCategoryId((int) $groupId);
            $item->setTitle($faker->sentence(1));
            $item->setKey(
                StrNormalize::toSnakeCase(trim($item->getTitle(), '.'))
            );
            $item->setOrdering($i);
            $item->setState(1);

            $options = [];

            if ($type === ProductAttributeType::SELECT()) {
                foreach (range(1, random_int(5, 8)) as $o) {
                    $options[] = new ListOption(
                        [
                            'uid' => tid(),
                            'text' => $text = $faker->word(),
                            'value' => strtolower($text)
                        ]
                    );
                }
            }

            $item->setOptions($options);

            $mapper->createOne($item);

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(ProductAttribute::class);
    }
);

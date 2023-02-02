<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\ShopGo\Data\ListOption;
use Lyrasoft\ShopGo\Entity\ProductFeature;
use Lyrasoft\ShopGo\Enum\ProductFeatureType;
use Lyrasoft\ShopGo\ShopGoPackage;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\Utf8String;

use function Windwalker\tid;

/**
 * ProductFeature Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (ShopGoPackage $shopGo) use ($seeder, $orm, $db) {
        $faker = $seeder->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<ProductFeature> $mapper */
        $mapper = $orm->mapper(ProductFeature::class);

        foreach (range(1, 10) as $i) {
            $type = $faker->randomElement(
                [
                    ProductFeatureType::SELECT(),
                    ProductFeatureType::SELECT(),
                    ProductFeatureType::COLOR(),
                ]
            );

            $options = [];

            foreach (range(1, random_int(5, 8)) as $o) {
                $options[] = $option = new ListOption(
                    [
                        'uid' => tid(),
                        'text' => $text = Utf8String::ucwords($faker->word()),
                        'value' => strtolower($text)
                    ]
                );

                if ($type === ProductFeatureType::COLOR()) {
                    $option->setColor($faker->safeHexColor());
                }
            }

            $item = $mapper->createEntity();
            $item->setType($type);
            $item->setTitle($faker->sentence(1));
            $item->setDefault($faker->randomElement($options)['value']);
            $item->setOptions($options);
            $item->setNote($faker->sentence());
            $item->setOrdering($i);
            $item->setState(1);

            $mapper->createOne($item);

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(ProductFeature::class);
    }
);

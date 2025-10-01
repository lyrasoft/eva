<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\ShopGo\Entity\Manufacturer;
use Lyrasoft\ShopGo\ShopGoPackage;
use Lyrasoft\Luna\Entity\Tag;
use Lyrasoft\Luna\Entity\TagMap;
use Lyrasoft\Luna\Entity\User;
use Lyrasoft\Luna\Services\LocaleService;
use Unicorn\Utilities\SlugHelper;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\Utf8String;

/**
 * Manufacturer Seeder
 *
 * @var Seeder $seeder
 * @var ORM $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (ShopGoPackage $shopGo) use ($seeder, $orm, $db) {
        $faker = $seeder->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<Manufacturer> $mapper */
        $mapper = $orm->mapper(Manufacturer::class);

        /** @var EntityMapper<TagMap> $tagMapMapper */
        $tagMapMapper = $orm->mapper(TagMap::class);

        $userIds = $orm->findColumn(User::class, 'id')->dump();
        $tagIds = $orm->findColumn(Tag::class, 'id')->dump();
        $langCodes = LocaleService::getSeederLangCodes($orm);

        foreach (range(1, 30) as $i) {
            $langCode = $faker->randomElement($langCodes);
            $item = $mapper->createEntity();

            $faker = $seeder->faker($langCode);

            $item->setTitle(
                Utf8String::ucwords(
                    $faker->company()
                )
            );
            $item->setAlias(SlugHelper::safe($item->getTitle()));
            $item->setImage($faker->unsplashImage());
            $item->setIntrotext($faker->paragraph(5));
            $item->setState($faker->optional(0.7, 0)->passthrough(1));
            $item->setOrdering($i);
            $item->setMeta(
                [
                    'title' => $item->getTitle(),
                    'description' => $faker->paragraph(),
                    'keywords' => implode(',', $faker->words()),
                ]
            );
            $item->setCreated($faker->dateTimeThisYear());
            $item->setModified($item->getCreated()->modify('+10days'));
            $item->setCreatedBy((int) $faker->randomElement($userIds));
            $item->setLanguage($langCode);
            $item->setParams([]);

            $item = $mapper->createOne($item);

            foreach ($faker->randomElements($tagIds, 3) as $tagId) {
                $tagMapItem = $tagMapMapper->createEntity();

                $tagMapItem->setType('manufacturer');
                $tagMapItem->setTargetId($item->getId());
                $tagMapItem->setTagId((int) $tagId);

                $tagMapMapper->createOne($tagMapItem);
            }

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Manufacturer::class);
    }
);

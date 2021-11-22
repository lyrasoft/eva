<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use App\Entity\Portfolio;
use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Luna\Entity\Tag;
use Lyrasoft\Luna\Entity\TagMap;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Portfolio Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function () use ($seeder, $orm, $db) {
        $faker = $seeder->faker('zh_TW');

        $userIds = $orm->findColumn(User::class, 'id', [])->dump();
        $categoryIds = $orm->findColumn(Category::class, 'id', ['type' => 'portfolio'])->dump();
        $tagIds = $orm->findColumn(Tag::class, 'id')->dump();
        /** @var EntityMapper<Portfolio> $mapper */
        $mapper = $orm->mapper(Portfolio::class);

        foreach (range(1, 50) as $i) {
            $item = $mapper->createEntity();

            $item->setTitle($faker->sentence(3));
            $item->setSubtitle($faker->sentence(2));
            $item->setCategoryId((int) $faker->randomElement($categoryIds));
            $item->setDescription($faker->paragraph(10));
            $item->setCover($faker->unsplashImage(1600, 800));
            $item->setImages(
                $faker->unsplashImages(random_int(5, 8), 1000, 1000)
            );
            $item->setUrl($faker->url());
            $item->setState(1);
            $item->setOrdering($i);
            $item->setCreatedBy((int) $faker->randomElement($userIds));
            $item->setModifiedBy((int) $faker->randomElement($userIds));
            $item->setCreated($created = $faker->dateTimeThisYear());
            $item->setModified($created->modify('+10days'));

            /** @var Portfolio $item */
            $item = $mapper->createOne($item);

            foreach ($faker->randomElements($tagIds, random_int(3, 5)) as $tagId) {
                $map = new TagMap();
                $map->setTargetId($item->getId());
                $map->setTagId((int) $tagId);
                $map->setType('portfolio');

                $orm->createOne(TagMap::class, $map);
            }

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        //
    }
);

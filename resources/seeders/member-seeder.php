<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Lyrasoft\Member\Seeder;

use Lyrasoft\Member\Entity\Member;
use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Luna\Entity\Tag;
use Lyrasoft\Luna\Entity\TagMap;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Member Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function () use ($seeder, $orm, $db) {
        $faker = $seeder->faker('zh_TW');

        $userIds = $orm->findColumn(User::class, 'id', [])->dump();
        $categoryIds = $orm->findColumn(Category::class, 'id', ['type' => 'member'])->dump();
        $tagIds = $orm->findColumn(Tag::class, 'id')->dump();
        /** @var EntityMapper<Member> $mapper */
        $mapper = $orm->mapper(Member::class);

        foreach (range(1, 30) as $i) {
            $item = $mapper->createEntity();

            $item->setName($faker->name());
            $item->setCategoryId((int) $faker->randomElement($categoryIds));
            $item->setIntro($faker->paragraph(5));
            $item->setDescription($faker->paragraph(10));
            $item->setImage($faker->avatar(400));
            $item->setState(1);
            $item->setOrdering($i);
            $item->setCreatedBy((int) $faker->randomElement($userIds));
            $item->setModifiedBy((int) $faker->randomElement($userIds));
            $item->setCreated($created = $faker->dateTimeThisYear());
            $item->setModified($created->modify('+10days'));

            /** @var Member $item */
            $item = $mapper->createOne($item);

            foreach ($faker->randomElements($tagIds, random_int(3, 5)) as $tagId) {
                $map = new TagMap();
                $map->setTargetId($item->getId());
                $map->setTagId((int) $tagId);
                $map->setType('member');

                $orm->createOne(TagMap::class, $map);
            }

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Member::class);
    }
);

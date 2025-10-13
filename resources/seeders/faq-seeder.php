<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Faq\Entity\Faq;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Faq Seeder
 *
 * @var Seeder $seeder
 * @var ORM $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function () use ($seeder, $orm, $db) {
        $faker = $seeder->faker('zh_TW');

        $userIds = $orm->findColumn(User::class, 'id')->dump();
        $categoryIds = $orm->findColumn(Category::class, 'id', ['type' => 'faq'])->dump();

        /** @var EntityMapper<Faq> $mapper */
        $mapper = $orm->mapper(Faq::class);

        foreach (range(1, 50) as $i) {
            /** @var Faq $item */
            $item = $mapper->createEntity();

            $item->setCategoryId((int) $faker->randomElement($categoryIds));
            $item->setTitle($faker->sentence(2));
            $item->setImage($faker->unsplashImage());
            $item->setState($faker->optional(0.7, 0)->passthrough(1));
            $item->setDescription($faker->paragraph(5));
            $item->setOrdering($i);
            $item->setCreated($faker->dateTimeThisYear());
            $item->setModified($item->getCreated()->modify('+5 days'));
            $item->setCreatedBy((int) $faker->randomElement($userIds));
            $item->setModifiedBy((int) $faker->randomElement($userIds));
            $item->setParams([]);

            $mapper->createOne($item);

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Faq::class);
    }
);

<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Melo\Entity\Lesson;
use Lyrasoft\Melo\Entity\LessonCategoryMap;
use Lyrasoft\Melo\Entity\UserLessonMap;
use Lyrasoft\Melo\Enum\UserLessonStatus;
use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Luna\Entity\Tag;
use Lyrasoft\Luna\Entity\TagMap;
use Lyrasoft\Luna\Entity\User;
use Lyrasoft\Luna\Entity\UserRoleMap;
use Lyrasoft\Luna\Services\LocaleService;
use Unicorn\Utilities\SlugHelper;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;
use Windwalker\Query\Query;
use Windwalker\Utilities\Utf8String;

use function Windwalker\Query\qn;

/**
 * Lesson Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function () use ($seeder, $orm, $db) {
        $faker = $seeder->faker('zh-TW');

        /** @var EntityMapper<Lesson> $mapper */
        $mapper = $orm->mapper(Lesson::class);
        /** @var EntityMapper<LessonCategoryMap> $mapMapper */
        $mapMapper = $orm->mapper(LessonCategoryMap::class);
        $userLessonMapMapper = $orm->mapper(UserLessonMap::class);
        $tagIds = $orm->findColumn(Tag::class, 'id')->dump();

        $teacherIds = $orm->from(User::class)
            ->select('id')
            ->whereExists(
                fn(Query $query) => $query->from(UserRoleMap::class)
                    ->where('role_id', 'admin')
                    ->where('user_id', qn('user.id'))
            )
            ->loadColumn('id')
            ->dump(true);

        $userIds = $orm->from(User::class)
            ->select('id')
            ->whereExists(
                fn(Query $query) => $query->from(UserRoleMap::class)
                    ->where('role_id', 'member')
                    ->where('user_id', qn('user.id'))
            )
            ->loadColumn('id')
            ->dump(true);

        $categoryIds = $orm->findColumn(
            Category::class,
            'id',
            [
                'type' => 'lesson',
            ]
        )
            ->dump(true);

        $langCodes = LocaleService::getSeederLangCodes($orm);

        foreach (range(1, 50) as $i) {
            $langCode = $faker->randomElement($langCodes);
            $item = $mapper->createEntity();

            $faker = $seeder->faker($langCode);

            $item->setCategoryId((int) $faker->randomElement($categoryIds));
            $item->setTitle(
                Utf8String::ucwords(
                    $faker->sentence(3)
                )
            );
            $item->setTeacherId((int) $faker->randomElement($teacherIds));
            $item->setDescription($faker->paragraph(6));
            $item->setAcquired($faker->paragraph(6));
            $item->setAlias(SlugHelper::safe($item->getTitle()));
            $item->setImage($faker->unsplashImage(800, 600));
            $item->setState($faker->optional(0.7, 0)->passthrough(1));
            $item->setStartDate($faker->dateTimeThisYear());
            $item->setEndDate($item->getStartDate()->modify('+30days'));
            $item->setIsStepByStep((bool) $faker->optional(0.7, 0)->passthrough(1));
            $item->setHasCertificate((bool) $faker->optional(0.7, 0)->passthrough(1));
            $item->setIsFree((bool) $faker->optional(0.2, 0)->passthrough(1));
            $item->setIsSpecial((bool) $faker->optional(0.7, 0)->passthrough(1));

            if ($item->isFree()) {
                $item->setPrice(0);
            } else {
                $item->setPrice(random_int(3, 30) * 100);
            }

            if ($item->isSpecial()) {
                $item->setSpecialPrice($item->getPrice() * 0.9);
            }

            $item->setPassAverageScore($faker->randomElement([60, 70, 80]));
            $item->setPassMinScore($item->getPassAverageScore() - 10);
            $item->setOrdering($i);
            $item->setCreated($faker->dateTimeThisYear());
            $item->setModified($item->getCreated()->modify('+10days'));
            $item->setCreatedBy((int) $faker->randomElement($userIds));

            $item = $mapper->createOne($item);

            foreach ($faker->randomElements($tagIds, random_int(3, 5)) as $tagId) {
                $map = new TagMap();
                $map->setTagId((int) $tagId);
                $map->setType('lesson');
                $map->setTargetId($item->getId());

                $orm->createOne(TagMap::class, $map);

                $seeder->outCounting();
            }

            $category = $orm->findOne(Category::class, ['id' => $item->getCategoryId()]);

            $subCategoryIds = $orm->findColumn(
                Category::class,
                'id',
                [
                    'type' => 'lesson',
                    [
                        'lft', '>', $category?->getLft(),
                    ],
                    [
                        'rgt', '<', $category?->getRgt(),
                    ]
                ]
            )->dump(true);

            $map = $mapMapper->createEntity();

            $map->setCategoryId($item->getCategoryId());
            $map->setIsPrimary(true);
            $map->setLessonId($item->getId());

            $mapMapper->createOne($map);

            foreach ($subCategoryIds as $categoryId) {
                $map = $mapMapper->createEntity();

                $map->setCategoryId((int) $categoryId);
                $map->setLessonId($item->getId());
                $map->setIsPrimary(false);

                $mapMapper->createOne($map);

                $seeder->outCounting();
            }


            foreach ($faker->randomElements($userIds, 10) as $j) {
                $userLessonMap = $userLessonMapMapper->createEntity();

                $userLessonMap->setUserId((int) $j);
                $userLessonMap->setLessonId((int) $item->getId());
                $userLessonMap->setStatus($faker->randomElement(UserLessonStatus::values()));
                $userLessonMap->setCreated($faker->dateTimeThisYear());

                $userLessonMapMapper->createOne($userLessonMap);

                $seeder->outCounting();
            }
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Lesson::class);
        $seeder->truncate(LessonCategoryMap::class);
        $seeder->truncate(UserLessonMap::class);
    }
);

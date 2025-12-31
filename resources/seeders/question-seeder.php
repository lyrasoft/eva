<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Melo\Entity\Question;
use Lyrasoft\Melo\Entity\Segment;
use Lyrasoft\Melo\Enum\QuestionType;
use Lyrasoft\Melo\Enum\SegmentType;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\ORM;
use Windwalker\ORM\EntityMapper;
use Windwalker\Utilities\Utf8String;

/**
 * Question Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function () use ($seeder, $orm, $db) {
        $faker = $seeder->faker('zh_TW');

        /** @var EntityMapper<Question> $mapper */
        $mapper = $orm->mapper(Question::class);
        $segments = $orm->findList(Segment::class, ['type' => SegmentType::QUIZ]);
        $userIds = $orm->findColumn(User::class, 'id')->dump(true);

        /** @var Segment $segment */
        foreach ($segments as $segment) {
            foreach (range(1, 10) as $i) {
                $item = $mapper->createEntity();

                $item->setType($faker->randomElement(QuestionType::values()));
                $item->setTitle(
                    Utf8String::ucwords(
                        $faker->sentence(3)
                    )
                );
                $item->setLessonId($segment->getLessonId());
                $item->setSegmentId($segment->getId());
                $item->setContent($faker->paragraph());
                $item->setIsMultiple($item->getType() === QuestionType::MULTIPLE);
                $item->setImage($faker->unsplashImage());
                $item->setScore(10);
                $item->setState(1);
                $item->setOrdering($i);
                $item->setCreated($faker->dateTimeThisYear());
                $item->setModified($segment->getCreated()?->modify('+10days'));
                $item->setCreatedBy((int) $faker->randomElement($userIds));

                if ($item->getType() === QuestionType::BOOLEAN) {
                    $item->setAnswer($faker->randomElement(['0', '1']));
                }

                $mapper->createOne($item);

                $seeder->outCounting();
            }
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Question::class);
    }
);

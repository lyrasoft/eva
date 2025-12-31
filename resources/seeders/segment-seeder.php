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
use Lyrasoft\Melo\Entity\Segment;
use Lyrasoft\Melo\Entity\UserLessonMap;
use Lyrasoft\Melo\Entity\UserSegmentMap;
use Lyrasoft\Melo\Enum\SegmentType;
use Lyrasoft\Melo\Enum\UserSegmentStatus;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\Utf8String;

/**
 * Segment Seeder
 *
 * @var Seeder $seeder
 * @var ORM $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function () use ($seeder, $orm, $db) {
        $faker = $seeder->faker('zh_TW');
        /** @var EntityMapper<Segment> $mapper */
        $mapper = $orm->mapper(Segment::class);
        $mapMapper = $orm->mapper(UserSegmentMap::class);
        $lessonIds = $orm->findColumn(Lesson::class, 'id')->dump(true);
        $userIds = $orm->findColumn(User::class, 'id')->dump(true);

        foreach ($lessonIds as $lessonId) {
            $lessonStudentIds = $orm->findColumn(
                UserLessonMap::class,
                'user_id',
                [
                    'lesson_id' => $lessonId,
                ]
            )
                ->dump(true);

            foreach (range(1, random_int(3, 4)) as $i) {
                // Chapter
                $chapter = $mapper->createEntity();

                $chapter->setLessonId((int) $lessonId);
                $chapter->setType(SegmentType::DEFAULT);
                $chapter->setParentId(0);
                $chapter->setTitle(
                    Utf8String::ucwords(
                        $faker->sentence(3)
                    )
                );
                $chapter->setState($faker->optional(0.7, 0)->passthrough(1));
                $chapter->setOrdering($i);
                $chapter->setCreated($faker->dateTimeThisYear());
                $chapter->setModified($chapter->getCreated()?->modify('+10days'));
                $chapter->setCreatedBy((int) $faker->randomElement($userIds));

                $chapter = $mapper->createOne($chapter);

                $k = 1;

                foreach (SegmentType::values() as $type) {
                    if ($type === SegmentType::DEFAULT) {
                        continue;
                    }

                    // segment
                    ++$k;

                    $segment = $mapper->createEntity();

                    $segment->setLessonId((int) $lessonId);
                    $segment->setType($type);
                    $segment->setParentId($chapter->getId());
                    $segment->setTitle(
                        Utf8String::ucwords(
                            $faker->sentence(3)
                        )
                    );
                    $segment->setState($faker->optional(0.7, 0)->passthrough(1));
                    $segment->setPreview($faker->optional(0.7, false)->passthrough(true));
                    $segment->setOrdering($k);
                    $segment->setCreated($faker->dateTimeThisYear());
                    $segment->setModified($segment->getCreated()?->modify('+10days'));
                    $segment->setCreatedBy((int) $faker->randomElement($userIds));

                    if ($type === SegmentType::VIDEO) {
                        $segment->setSrc('https://lyratest.s3.amazonaws.com/emooc/sintel-short.mp4');
                        $segment->setCaptionSrc('https://lyratest.s3.amazonaws.com/emooc/sintel-subtitles-en.vtt');
                        $segment->setFilename('sintel-short.mp4');
                        $segment->setExt('mp4');
                        $segment->setDuration(random_int(40, 240));
                    }

                    if ($type === SegmentType::HOMEWORK) {
                        $segment->setContent($faker->paragraph(5));
                    }

                    if ($type === SegmentType::QUIZ) {
                        $segment->setCanSkip($faker->optional(0.8, true)->passthrough(false));
                    }

                    $segment = $mapper->createOne($segment);

                    foreach ($lessonStudentIds as $studentId) {
                        /** @var UserSegmentMap $map */
                        $map = $mapMapper->createEntity();

                        $map->setUserId((int) $studentId);
                        $map->setLessonId((int) $lessonId);
                        $map->setSegmentId($segment->getId());
                        $map->setSegmentType($segment->getType());
                        $map->setDescription($faker->paragraph());
                        $map->setCreated($faker->dateTimeThisYear());

                        if ($map->getSegmentType() === SegmentType::QUIZ) {
                            $map->setScore(random_int(60, 100));
                            $map->setStatus(
                                $faker->randomElement(
                                    [
                                        UserSegmentStatus::PROCESS,
                                        UserSegmentStatus::PASSED,
                                        UserSegmentStatus::FAILED,
                                    ]
                                )
                            );
                        }

                        if ($map->getSegmentType() === SegmentType::HOMEWORK) {
                            $map->setAssignment($faker->unsplashImage());
                            $map->setStatus(
                                $faker->randomElement(
                                    [
                                        UserSegmentStatus::PENDING,
                                        UserSegmentStatus::DONE,
                                    ]
                                )
                            );
                            $map->setAssignmentUploadTime($map->getCreated()?->modify('+3 days'));
                        }

                        $mapMapper->createOne($map);

                        $seeder->outCounting();
                    }
                }
            }
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Segment::class);
        $seeder->truncate(UserSegmentMap::class);
    }
);

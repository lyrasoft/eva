<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Melo\Entity\Lesson;
use Lyrasoft\Melo\Entity\Segment;
use Lyrasoft\Melo\Entity\UserLessonMap;
use Lyrasoft\Melo\Entity\UserSegmentMap;
use Lyrasoft\Melo\Enum\SegmentType;
use Lyrasoft\Melo\Enum\UserSegmentStatus;
use Lyrasoft\Luna\Entity\User;
use Lyrasoft\Melo\Features\Section\AbstractSection;
use Lyrasoft\Melo\Features\Section\Homework\HomeworkSection;
use Lyrasoft\Melo\Features\Section\Quiz\QuizSection;
use Lyrasoft\Melo\Features\Section\SectionComposer;
use Lyrasoft\Melo\Features\Section\Video\VideoSection;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;
use Windwalker\Utilities\Utf8String;

return new /** Segment Seeder */ class extends AbstractSeeder
{
    #[SeedImport]
    public function import(SectionComposer $sectionComposer): void
    {
        $faker = $this->faker('zh_TW');
        /** @var EntityMapper<Segment> $mapper */
        $mapper = $this->orm->mapper(Segment::class);
        $mapMapper = $this->orm->mapper(UserSegmentMap::class);
        $lessonIds = $this->orm->findColumn(Lesson::class, 'id')->dump(true);
        $userIds = $this->orm->findColumn(User::class, 'id')->dump(true);

        /** @var array<class-string<AbstractSection>> $defines */
        $defines = $sectionComposer->getDefines();

        foreach ($lessonIds as $lessonId) {
            $lessonStudentIds = $this->orm->findColumn(
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

                $chapter->lessonId = (int) $lessonId;
                $chapter->type = '';
                $chapter->parentId = 0;
                $chapter->title = Utf8String::ucwords(
                    $faker->sentence(3)
                );
                $chapter->state = $faker->optional(0.7, 0)->passthrough(1);
                $chapter->ordering = $i;
                $chapter->created = $faker->dateTimeThisYear();
                $chapter->modified = $chapter->created?->modify('+10days');
                $chapter->createdBy = (int) $faker->randomElement($userIds);

                $chapter = $mapper->createOne($chapter);

                $k = 1;

                foreach ($defines as $define) {
                    // segment
                    ++$k;

                    $segment = $mapper->createEntity();

                    $segment->lessonId = (int) $lessonId;
                    $segment->type = $type = $define::id();
                    $segment->parentId = $chapter->id;
                    $segment->title = Utf8String::ucwords(
                        $faker->sentence(3)
                    );
                    $segment->state = $faker->boolean(70) ? 1 : 0;
                    $segment->preview = !$faker->boolean(70);
                    $segment->ordering = $k;
                    $segment->created = $faker->dateTimeThisYear();
                    $segment->modified = $segment->created?->modify('+10days');
                    $segment->createdBy = (int) $faker->randomElement($userIds);

                    if ($type === VideoSection::id()) {
                        $segment->src = 'https://lyratest.s3.amazonaws.com/emooc/sintel-short.mp4';
                        $segment->captionSrc = 'https://lyratest.s3.amazonaws.com/emooc/sintel-subtitles-en.vtt';
                        $segment->filename = 'sintel-short.mp4';
                        $segment->ext = 'mp4';
                        $segment->duration = random_int(40, 240);
                    }

                    if ($type === HomeworkSection::id()) {
                        $segment->content = $faker->paragraph(5);
                    }

                    if ($type === QuizSection::id()) {
                        $segment->canSkip = !$faker->boolean(80);
                    }

                    $segment = $mapper->createOne($segment);

                    foreach ($lessonStudentIds as $studentId) {
                        /** @var UserSegmentMap $map */
                        $map = $mapMapper->createEntity();

                        $map->userId = (int) $studentId;
                        $map->lessonId = (int) $lessonId;
                        $map->segmentId = $segment->id;
                        $map->segmentType = $segment->type;
                        $map->description = $faker->paragraph();
                        $map->created = $faker->dateTimeThisYear();

                        if ($map->segmentType === QuizSection::id()) {
                            $map->score = random_int(60, 100);
                            $map->status = $faker->randomElement(
                                [
                                    UserSegmentStatus::PROCESS,
                                    UserSegmentStatus::PASSED,
                                    UserSegmentStatus::FAILED,
                                ]
                            );
                        }

                        if ($map->segmentType === HomeworkSection::id()) {
                            $map->assignment = $faker->unsplashImage();
                            $map->status = $faker->randomElement(
                                [
                                    UserSegmentStatus::PENDING,
                                    UserSegmentStatus::DONE,
                                ]
                            );
                            $map->assignmentUploadTime = $map->created?->modify('+3 days');
                            $map->frontShow = $faker->boolean(80);
                        }

                        $mapMapper->createOne($map);

                        $this->printCounting();
                    }
                }
            }
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Segment::class);
        $this->truncate(UserSegmentMap::class);
    }
};

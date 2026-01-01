<?php

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
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;
use Windwalker\Query\Query;
use Windwalker\Utilities\Utf8String;

use function Windwalker\Query\qn;

return new /** Lesson Seeder */ class extends AbstractSeeder
{
    #[SeedImport]
    public function import(): void
    {
        $faker = $this->faker('zh-TW');

        /** @var EntityMapper<Lesson> $mapper */
        $mapper = $this->orm->mapper(Lesson::class);
        /** @var EntityMapper<LessonCategoryMap> $mapMapper */
        $mapMapper = $this->orm->mapper(LessonCategoryMap::class);
        $userLessonMapMapper = $this->orm->mapper(UserLessonMap::class);
        $tagIds = $this->orm->findColumn(Tag::class, 'id')->dump();

        $teacherIds = $this->orm->from(User::class)
            ->select('id')
            ->whereExists(
                fn(Query $query) => $query->from(UserRoleMap::class)
                    ->where('role_id', 'admin')
                    ->where('user_id', qn('user.id'))
            )
            ->loadColumn('id')
            ->dump(true);

        $userIds = $this->orm->from(User::class)
            ->select('id')
            ->whereExists(
                fn(Query $query) => $query->from(UserRoleMap::class)
                    ->where('role_id', 'member')
                    ->where('user_id', qn('user.id'))
            )
            ->loadColumn('id')
            ->dump(true);

        $categoryIds = $this->orm->findColumn(
            Category::class,
            'id',
            [
                'type' => 'lesson',
            ]
        )
            ->dump(true);

        $langCodes = LocaleService::getSeederLangCodes($this->orm);

        foreach (range(1, 50) as $i) {
            $langCode = $faker->randomElement($langCodes);
            $item = $mapper->createEntity();

            $faker = $this->faker($langCode);

            $item->categoryId = (int) $faker->randomElement($categoryIds);
            $item->title = Utf8String::ucwords(
                $faker->sentence(3)
            );
            $item->teacherId = (int) $faker->randomElement($teacherIds);
            $item->description = $faker->paragraph(6);
            $item->acquired = $faker->paragraph(6);
            $item->alias = SlugHelper::safe($item->title);
            $item->image = $faker->unsplashImage(800, 600);
            $item->state = $faker->optional(0.7, 0)->passthrough(1);
            $item->startDate = $faker->dateTimeThisYear();
            $item->endDate = $item->startDate->modify('+30days');
            $item->isStepByStep = (bool) $faker->optional(0.7, 0)->passthrough(1);
            $item->hasCertificate = (bool) $faker->optional(0.7, 0)->passthrough(1);
            $item->isFree = (bool) $faker->optional(0.2, 0)->passthrough(1);
            $item->isSpecial = (bool) $faker->optional(0.7, 0)->passthrough(1);

            if ($item->isFree) {
                $item->price = 0;
            } else {
                $item->price = random_int(3, 30) * 100;
            }

            if ($item->isSpecial) {
                $item->specialPrice = $item->price * 0.9;
            }

            $item->passAverageScore = $faker->randomElement([60, 70, 80]);
            $item->passMinScore = $item->passAverageScore - 10;
            $item->ordering = $i;
            $item->created = $faker->dateTimeThisYear();
            $item->modified = $item->created->modify('+10days');
            $item->createdBy = (int) $faker->randomElement($userIds);

            $item = $mapper->createOne($item);

            foreach ($faker->randomElements($tagIds, random_int(3, 5)) as $tagId) {
                $map = new TagMap();
                $map->tagId = (int) $tagId;
                $map->type = 'lesson';
                $map->targetId = $item->id;

                $this->orm->createOne(TagMap::class, $map);

                $this->printCounting();
            }

            $category = $this->orm->findOne(Category::class, ['id' => $item->categoryId]);

            $subCategoryIds = $this->orm->findColumn(
                Category::class,
                'id',
                [
                    'type' => 'lesson',
                    [
                        'lft', '>', $category?->lft,
                    ],
                    [
                        'rgt', '<', $category?->rgt,
                    ]
                ]
            )->dump(true);

            $map = $mapMapper->createEntity();

            $map->categoryId = $item->categoryId;
            $map->isPrimary = true;
            $map->lessonId = $item->id;

            $mapMapper->createOne($map);

            foreach ($subCategoryIds as $categoryId) {
                $map = $mapMapper->createEntity();

                $map->categoryId = (int) $categoryId;
                $map->lessonId = $item->id;
                $map->isPrimary = false;

                $mapMapper->createOne($map);

                $this->printCounting();
            }


            foreach ($faker->randomElements($userIds, 10) as $j) {
                $userLessonMap = $userLessonMapMapper->createEntity();

                $userLessonMap->userId = (int) $j;
                $userLessonMap->lessonId = (int) $item->id;
                $userLessonMap->status = $faker->randomElement(UserLessonStatus::values());
                $userLessonMap->created = $faker->dateTimeThisYear();

                $userLessonMapMapper->createOne($userLessonMap);

                $this->printCounting();
            }
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Lesson::class);
        $this->truncate(LessonCategoryMap::class);
        $this->truncate(UserLessonMap::class);
    }
};

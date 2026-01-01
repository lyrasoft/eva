<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Melo\Entity\Question;
use Lyrasoft\Melo\Entity\Segment;
use Lyrasoft\Melo\Enum\QuestionType;
use Lyrasoft\Melo\Enum\SegmentType;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;
use Windwalker\Utilities\Utf8String;

return new /** Question Seeder */ class extends AbstractSeeder
{
    #[SeedImport]
    public function import(): void
    {
        $faker = $this->faker('zh_TW');

        /** @var EntityMapper<Question> $mapper */
        $mapper = $this->orm->mapper(Question::class);
        $segments = $this->orm->findList(Segment::class, ['type' => SegmentType::QUIZ]);
        $userIds = $this->orm->findColumn(User::class, 'id')->dump(true);

        /** @var Segment $segment */
        foreach ($segments as $segment) {
            foreach (range(1, 10) as $i) {
                $item = $mapper->createEntity();

                $item->type = $faker->randomElement(QuestionType::values());
                $item->title = Utf8String::ucwords(
                    $faker->sentence(3)
                );
                $item->lessonId = $segment->lessonId;
                $item->segmentId = $segment->id;
                $item->content = $faker->paragraph();
                $item->isMultiple = $item->type === QuestionType::MULTIPLE;
                $item->image = $faker->unsplashImage();
                $item->score = 10;
                $item->state = 1;
                $item->ordering = $i;
                $item->created = $faker->dateTimeThisYear();
                $item->modified = $segment->created?->modify('+10days');
                $item->createdBy = (int) $faker->randomElement($userIds);

                if ($item->type === QuestionType::BOOLEAN) {
                    $item->answer = $faker->randomElement(['0', '1']);
                }

                $mapper->createOne($item);

                $question = $item;

                // Options
                if ($question->type === QuestionType::BOOLEAN) {
                    $this->printCounting();
                    continue;
                }

                $total = random_int(2, 4);

                $answers = array_fill(0, $total, false);

                if ($question->isMultiple) {
                    $trueCount = random_int(2, $total);

                    foreach (range(0, $trueCount) as $k) {
                        $answers[$k] = true;
                    }
                } else {
                    $answers[0] = true;
                }

                shuffle($answers);

                foreach (range(1, $total) as $j) {
                    $option = $mapper->createEntity();

                    $option->title = Utf8String::ucwords(
                        $faker->sentence(3)
                    );
                    $option->questionId = $question->id;
                    $option->state = 1;
                    $option->ordering = $j;
                    $option->isAnswer = $answers[$j - 1];

                    $mapper->createOne($option);
                }

                $this->printCounting();
            }
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Question::class);
    }
};

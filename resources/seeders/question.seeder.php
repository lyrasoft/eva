<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Melo\Entity\MeloOption;
use Lyrasoft\Melo\Entity\Question;
use Lyrasoft\Melo\Entity\Segment;
use Lyrasoft\Melo\Enum\QuestionType;
use Lyrasoft\Melo\Enum\SegmentType;
use Lyrasoft\Luna\Entity\User;
use Lyrasoft\Melo\Features\Question\AbstractQuestion;
use Lyrasoft\Melo\Features\Question\Boolean\BooleanQuestion;
use Lyrasoft\Melo\Features\Question\MultiSelect\MultiSelectQuestion;
use Lyrasoft\Melo\Features\Question\QuestionComposer;
use Lyrasoft\Melo\Features\Section\Quiz\QuizSection;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;
use Windwalker\Utilities\Utf8String;

return new /** Question Seeder */ class extends AbstractSeeder
{
    #[SeedImport]
    public function import(QuestionComposer $questionComposer): void
    {
        $faker = $this->faker('zh_TW');

        /** @var EntityMapper<Question> $mapper */
        $mapper = $this->orm->mapper(Question::class);
        $segments = $this->orm->findList(Segment::class, ['type' => QuizSection::id()]);
        $userIds = $this->orm->findColumn(User::class, 'id')->dump(true);

        /** @var array<class-string<AbstractQuestion>> $defines */
        $defines = $questionComposer->getDefines();

        /** @var Segment $segment */
        foreach ($segments as $segment) {
            foreach (range(1, 10) as $i) {
                $item = $mapper->createEntity();

                $item->type = $faker->randomElement(array_keys($defines));
                $item->title = Utf8String::ucwords(
                    $faker->sentence(3)
                );
                $item->lessonId = $segment->lessonId;
                $item->segmentId = $segment->id;
                $item->content = $faker->paragraph();
                $item->isMultiple = $item->type === MultiSelectQuestion::id();
                $item->image = $faker->unsplashImage();
                $item->score = 10;
                $item->state = 1;
                $item->ordering = $i;
                $item->created = $faker->dateTimeThisYear();
                $item->modified = $segment->created?->modify('+10days');
                $item->createdBy = (int) $faker->randomElement($userIds);

                if ($item->type === BooleanQuestion::id()) {
                    $item->answer = $faker->boolean() ? '1' : '0';
                }

                $mapper->createOne($item);

                $question = $item;

                // Options
                if ($question->type === BooleanQuestion::id()) {
                    $this->printCounting();
                    continue;
                }

                $total = random_int(2, 4);

                $answers = array_fill(0, $total, false);

                if ($question->type === MultiSelectQuestion::id()) {
                    $trueCount = random_int(2, $total);

                    foreach (range(0, $trueCount) as $k) {
                        $answers[$k] = true;
                    }
                } else {
                    $answers[0] = true;
                }

                shuffle($answers);

                foreach (range(1, $total) as $j) {
                    $option = new MeloOption();

                    $option->title = Utf8String::ucwords(
                        $faker->sentence(3)
                    );
                    $option->questionId = $question->id;
                    $option->state = 1;
                    $option->ordering = $j;
                    $option->isAnswer = $answers[$j - 1];

                    $this->orm->createOne($option);
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

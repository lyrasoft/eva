<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Feedback\Entity\Comment;
use Lyrasoft\Feedback\Entity\Rating;
use Lyrasoft\Feedback\Service\RatingService;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;

return new /** Ranting Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(RatingService $ratingService): void
    {
        $faker = $this->faker('en_US');

        $type = 'comment';

        $userIds = $this->orm->findColumn(User::class, 'id')->map('intval')->dump();
        $comments = $this->orm->findList(Comment::class)->all();

        /** @var Comment $comment */
        foreach ($comments as $comment) {
            $ratedUserIds = $faker->randomElements($userIds, random_int(0, 15));
            $time = $faker->dateTimeThisYear();

            foreach ($ratedUserIds as $ratedUserId) {
                $time = $time->modify('+12hour');

                $ratingService->addRating(
                    $type,
                    $comment->id,
                    $ratedUserId,
                    extra: function (Rating $item) use ($time) {
                        $item->created = $time;
                    }
                );

                $this->printCounting();
            }

            $ratingService->reorderRatings($type, $comment->id);

            $rating = $ratingService->countRatings($type, $comment->id);

            $comment->rating = $rating;

            $this->orm->updateOne($comment);
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Rating::class);
    }
};

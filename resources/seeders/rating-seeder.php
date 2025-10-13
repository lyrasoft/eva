<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Feedback\Entity\Comment;
use Lyrasoft\Feedback\Entity\Rating;
use Lyrasoft\Feedback\Service\RatingService;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\ORM;

/**
 * Ranting Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (RatingService $ratingService) use ($seeder, $orm, $db) {
        $faker = $seeder->faker('en_US');

        $type = 'comment';

        $userIds = $orm->findColumn(User::class, 'id')->map('intval')->dump();
        $comments = $orm->findList(Comment::class)->all();

        /** @var Comment $comment */
        foreach ($comments as $comment) {
            $ratedUserIds = $faker->randomElements($userIds, random_int(0, 15));
            $time = $faker->dateTimeThisYear();

            foreach ($ratedUserIds as $ratedUserId) {
                $time = $time->modify('+12hour');

                $ratingService->addRating(
                    $type,
                    $comment->getId(),
                    $ratedUserId,
                    extra: function (Rating $item) use ($time) {
                        $item->setCreated($time);
                    }
                );

                $seeder->outCounting();
            }

            $ratingService->reorderRatings($type, $comment->getId());

            $rating = $ratingService->countRatings($type, $comment->getId());

            $comment->setRating($rating);

            $orm->updateOne($comment);
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Rating::class);
    }
);

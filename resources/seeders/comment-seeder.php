<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Feedback\Entity\Comment;
use Lyrasoft\Feedback\Service\CommentService;
use Lyrasoft\Luna\Entity\Article;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Comment Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (CommentService $commentService) use ($seeder, $orm, $db) {
        $faker = $seeder->faker('en_US');

        $type = 'article';

        /** @var EntityMapper<Comment> $mapper */
        $mapper = $orm->mapper(Comment::class);

        $articleIds = $orm->findColumn(Article::class, 'id')->map('intval')->dump();
        $userIds = $orm->findColumn(User::class, 'id')->map('intval')->dump();

        foreach ($articleIds as $articleId) {
            foreach (range(1, random_int(2, 5)) as $i) {
                $userId = $faker->randomElement($userIds);

                $item = $commentService->addComment(
                    $type,
                    $articleId,
                    $faker->paragraph(4),
                    $userId,
                    extra: function (Comment $item) use ($faker) {
                        $item->setTitle($faker->sentence(2));
                        $item->setCreated($faker->dateTimeThisYear());
                        $item->setOrdering($item->count() + 1);
                    }
                );

                $singleReply = (bool) random_int(0, 1);

                // Reply
                if ($singleReply) {
                    $commentService->addInstantReply(
                        $item,
                        $faker->paragraph(3),
                        $faker->randomElement($userIds),
                        $item->getCreated()->modify('+1 day')
                    );
                } else {
                    $commentService->addSubReply(
                        $item,
                        $faker->paragraph(3),
                        $faker->randomElement($userIds),
                        extra: function (Comment $reply) use ($item, $faker) {
                            $reply->setTitle('Re: ' . $item->getTitle());
                            $reply->setCreated($item->getCreated()->modify('+1 day'));
                            $reply->setOrdering($reply->count() + 1);
                        }
                    );
                }

                // $commentService->reorderComments(
                //     $item->getType(),
                //     $item->getTargetId(),
                //     $item->getId()
                // );

                $seeder->outCounting();
            }

            // $commentService->reorderComments(
            //     $item->getType(),
            //     $item->getTargetId(),
            // );
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Comment::class);
    }
);

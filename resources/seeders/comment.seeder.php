<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Feedback\Entity\Comment;
use Lyrasoft\Feedback\Service\CommentService;
use Lyrasoft\Luna\Entity\Article;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;

return new /** Comment Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(CommentService $commentService): void
    {
        $faker = $this->faker('en_US');

        $type = 'article';

        /** @var EntityMapper<Comment> $mapper */
        $mapper = $this->orm->mapper(Comment::class);

        $articleIds = $this->orm->findColumn(Article::class, 'id')->map('intval')->dump();
        $userIds = $this->orm->findColumn(User::class, 'id')->map('intval')->dump();

        foreach ($articleIds as $articleId) {
            foreach (range(1, random_int(2, 5)) as $i) {
                $userId = $faker->randomElement($userIds);

                $item = $commentService->addComment(
                    $type,
                    $articleId,
                    $faker->paragraph(4),
                    $userId,
                    extra: function (Comment $item) use ($faker) {
                        $item->title = $faker->sentence(2);
                        $item->created = $faker->dateTimeThisYear();
                        $item->ordering = $item->count() + 1;
                    }
                );

                $singleReply = (bool) random_int(0, 1);

                // Reply
                if ($singleReply) {
                    $commentService->addInstantReply(
                        $item,
                        $faker->paragraph(3),
                        $faker->randomElement($userIds),
                        $item->created->modify('+1 day')
                    );
                } else {
                    $commentService->addSubReply(
                        $item,
                        $faker->paragraph(3),
                        $faker->randomElement($userIds),
                        extra: function (Comment $reply) use ($item, $faker) {
                            $reply->title = 'Re: ' . $item->title;
                            $reply->created = $item->created->modify('+1 day');
                            $reply->ordering = $reply->count() + 1;
                        }
                    );
                }

                // $commentService->reorderComments(
                //     $item->getType(),
                //     $item->getTargetId(),
                //     $item->getId()
                // );

                $this->printCounting();
            }

            // $commentService->reorderComments(
            //     $item->getType(),
            //     $item->getTargetId(),
            // );
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Comment::class);
    }
};

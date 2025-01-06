<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Comment;
use Lyrasoft\Luna\User\UserEntityInterface;
use Lyrasoft\Luna\User\UserService;
use Windwalker\DI\Attributes\Service;
use Windwalker\ORM\ORM;
use Windwalker\ORM\SelectorQuery;
use Windwalker\Query\Query;

#[Service]
class CommentService
{
    public function __construct(protected ORM $orm, protected UserService $userService)
    {
    }

    public function createCommentItem(
        string|\BackedEnum $type,
        mixed $targetId,
        string $content,
        mixed $user = null,
    ): Comment {
        $user = $this->toUser($user);

        $item = $this->orm->createEntity(Comment::class);

        $item->setType($type);
        $item->setTargetId($targetId);
        $item->setUserId($user->getId());
        $item->setNickname($user->getName());

        if (method_exists($user, 'getEmail')) {
            $item->setEmail($user->getEmail());
        }

        if (method_exists($user, 'getAvatar')) {
            $item->setAvatar($user->getAvatar());
        }

        $item->setContent($content);
        $item->setState(1);

        return $item;
    }

    public function addComment(
        string|\BackedEnum $type,
        mixed $targetId,
        string $content,
        mixed $user = null,
        \Closure|array|null $extra = null,
    ): Comment {
        $item = $this->createCommentItem(
            $type,
            $targetId,
            $content,
            $user,
        );

        $item = $this->handleExtraData($extra, $item);

        $this->orm->createOne($item);

        return $item;
    }

    public function addInstantReply(
        mixed $comment,
        string $replyContent,
        mixed $user = null,
        \DateTimeInterface|string $time = 'now',
    ) {
        if (!$comment instanceof Comment) {
            $comment = $this->orm->mustFindOne(Comment::class, $comment);
        }

        $user = $this->toUser($user);

        $comment->setReplyUserId($user->getId());
        $comment->setReply($replyContent);
        $comment->setLastReplyAt($time);

        $this->orm->updateOne($comment);

        return $comment;
    }

    public function addSubReply(
        mixed $parent,
        string $replyContent,
        mixed $user = null,
        \Closure|array|null $extra = null,
    ): Comment {
        if (!$parent instanceof Comment) {
            $parent = $this->orm->mustFindOne(Comment::class, $parent);
        }

        $user = $this->toUser($user);

        $reply = $this->createCommentItem(
            $parent->getType(),
            $parent->getTargetId(),
            $replyContent,
            $user,
        );
        $reply->setParentId($parent->getId());

        $reply = $this->handleExtraData($extra, $reply);

        /** @var Comment $reply */
        $reply = $this->orm->createOne($reply);

        // Update parent
        $parent->setReplyUserId($user->getId());
        $parent->setLastReplyAt($reply->getCreated());
        $parent->setLastReplyId($reply->getId());

        $this->orm->updateOne($parent);

        return $reply;
    }

    protected function toUser(mixed $user): UserEntityInterface
    {
        if ($user instanceof UserEntityInterface) {
            return $user;
        }

        return $this->userService->getUser($user);
    }

    protected function handleExtraData(array|\Closure|null $extra, Comment $item): Comment
    {
        if ($extra instanceof \Closure) {
            $item = $extra($item) ?? $item;
        } elseif ($extra) {
            $item = $this->orm->hydrateEntity($extra, $item);
        }

        return $item;
    }

    public function countComments(
        string|\BackedEnum $type,
        mixed $targetId,
        mixed $parentId = null,
        bool $lock = false
    ): int {
        return (int) $this->createCommentQuery($type, $targetId, $parentId)
            ->selectRaw('COUNT(*) AS count')
            ->tapIf(
                $lock,
                fn (Query $query) => $query->forUpdate()
            )
            ->result();
    }

    public function reorderComments(
        string|\BackedEnum $type,
        mixed $targetId,
        mixed $parentId = null,
    ): void {
        $comments = $this->createCommentQuery($type, $targetId, $parentId)
            ->order('ordering', 'ASC')
            ->all(Comment::class);

        $id = 0;
        $ordering = 0;

        $query = $this->orm->update(Comment::class)
            ->whereRaw('id = :id')
            ->setRaw('ordering = :ordering')
            ->bindParam('id', $id)
            ->bindParam('ordering', $ordering);

        /** @var Comment $comment */
        foreach ($comments as $i => $comment) {
            $id = $comment->getId();
            $ordering = $i + 1;

            $query->execute();
        }
    }

    protected function createCommentQuery(
        \BackedEnum|string $type,
        mixed $targetId,
        mixed $parentId = null
    ): SelectorQuery {
        return $this->orm->from(Comment::class)
            ->where('type', $type)
            ->tapIf(
                $parentId !== null,
                fn(Query $query) => $query->where('parent_id', $parentId),
                fn(Query $query) => $query->whereRaw(
                    'IFNULL(parent_id, 0) IN (0, \'\')'
                )
            )
            ->where('target_id', $targetId);
    }
}

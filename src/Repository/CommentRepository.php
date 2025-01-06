<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use Lyrasoft\Luna\Entity\User;
use Unicorn\Attributes\ConfigureAction;
use Unicorn\Attributes\Repository;
use Unicorn\Repository\Actions\BatchAction;
use Unicorn\Repository\Actions\DeleteAction;
use Unicorn\Repository\Actions\ReorderAction;
use Unicorn\Repository\Actions\SaveAction;
use Unicorn\Repository\ListRepositoryInterface;
use Unicorn\Repository\ListRepositoryTrait;
use Unicorn\Repository\ManageRepositoryInterface;
use Unicorn\Repository\ManageRepositoryTrait;
use Unicorn\Selector\ListSelector;
use Windwalker\ORM\SelectorQuery;

#[Repository(entityClass: Comment::class)]
class CommentRepository implements ManageRepositoryInterface, ListRepositoryInterface
{
    use ManageRepositoryTrait;
    use ListRepositoryTrait;

    public function getListSelector(string|\BackedEnum $type): ListSelector
    {
        $selector = $this->createSelector();

        $selector->from(Comment::class)
            ->leftJoin(User::class)
            ->leftJoin(
                User::class,
                'reply_user',
                'reply_user.id',
                'comment.reply_user_id'
            )
            ->leftJoin(
                Comment::class,
                'last_reply',
                'last_reply.id',
                'comment.last_reply_id'
            )
            ->where('comment.type', $type)
            ->whereRaw('IFNULL(comment.parent_id, \'\') IN (%q)', ['', 0]);

        return $selector;
    }

    public function getFrontListSelector(string|\BackedEnum $type): ListSelector
    {
        $selector = $this->createSelector();

        $selector->from(Comment::class)
            ->leftJoin(User::class)
            ->where('comment.state', 1)
            ->where('comment.type', $type)
            ->whereRaw('IFNULL(comment.parent_id, \'\') IN (%q)', ['', 0]);

        return $selector;
    }

    #[ConfigureAction(SaveAction::class)]
    protected function configureSaveAction(SaveAction $action): void
    {
        //
    }

    #[ConfigureAction(ReorderAction::class)]
    protected function configureReorderAction(ReorderAction $action): void
    {
        //
    }

    #[ConfigureAction(BatchAction::class)]
    protected function configureBatchAction(BatchAction $action): void
    {
        //
    }

    #[ConfigureAction(DeleteAction::class)]
    protected function configureDeleteAction(DeleteAction $action): void
    {
        //
    }
}

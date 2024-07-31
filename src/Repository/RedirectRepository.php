<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Redirect;
use Unicorn\Attributes\ConfigureAction;
use Unicorn\Attributes\Repository;
use Unicorn\Repository\Actions\BatchAction;
use Unicorn\Repository\Actions\ReorderAction;
use Unicorn\Repository\Actions\SaveAction;
use Unicorn\Repository\ListRepositoryInterface;
use Unicorn\Repository\ListRepositoryTrait;
use Unicorn\Repository\ManageRepositoryInterface;
use Unicorn\Repository\ManageRepositoryTrait;
use Unicorn\Selector\ListSelector;
use Windwalker\ORM\SelectorQuery;
use Windwalker\Query\Query;

#[Repository(entityClass: Redirect::class)]
class RedirectRepository implements ManageRepositoryInterface, ListRepositoryInterface
{
    use ManageRepositoryTrait;
    use ListRepositoryTrait;

    public function getListSelector(): ListSelector
    {
        $selector = $this->createSelector();

        $selector->from(Redirect::class);

        $selector->addAllowFields(
            'redirect.params ->> regex',
            'redirect.params ->> not_found_only',
            'redirect.params ->> handle_lang',
        );

        return $selector;
    }

    public function getAvailableSelector(string|\BackedEnum|array|null $type): ListSelector
    {
        $selector = $this->createSelector();

        $selector->from(Redirect::class)
            ->tapIf(
                $type !== null,
                fn (ListSelector $selector) => $selector->where('redirect.type', $type)
            )
            ->where('redirect.state', 1);

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
        $action->setReorderGroupHandler(
            function (Query $query, Redirect $entity) {
                $query->where('type', $entity->getType());
            }
        );
    }

    #[ConfigureAction(BatchAction::class)]
    protected function configureBatchAction(BatchAction $action): void
    {
        //
    }
}

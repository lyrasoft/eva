<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;
use App\Entity\EventStage;
use App\Entity\Venue;
use Lyrasoft\Luna\Entity\Category;
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

#[Repository(entityClass: EventStage::class)]
class EventStageRepository implements ManageRepositoryInterface, ListRepositoryInterface
{
    use ManageRepositoryTrait;
    use ListRepositoryTrait;

    public function getListSelector(): ListSelector
    {
        $selector = $this->createSelector();

        $selector->from(EventStage::class)
            ->leftJoin(Venue::class);

        return $selector;
    }

    public function getAvailableListSelector(): ListSelector
    {
        $selector = $this->createSelector();

        $selector->from(EventStage::class)
            ->leftJoin(Venue::class)
            ->leftJoin(Event::class)
            ->leftJoin(
                Category::class,
                'category',
                'category.id',
                'event.category_id'
            );

        $selector->where('event_stage.state', 1);
        $selector->where('event.state', 1);
        $selector->where('category.state', 1);

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
}

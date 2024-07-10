<?php

declare(strict_types=1);

namespace App\Module\Front\EventStage;

use App\Entity\EventStage;
use App\Repository\EventStageRepository;
use Lyrasoft\Luna\Module\Front\Category\CategoryViewTrait;
use Unicorn\Selector\ListSelector;
use Unicorn\View\ORMAwareViewModelTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewMetadata;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\Query\Query;

use function Windwalker\chronos;

#[ViewModel(
    layout: 'event-stage-list',
    js: 'event-stage-list.js'
)]
class EventStageListView implements ViewModelInterface
{
    use ORMAwareViewModelTrait;
    use CategoryViewTrait;

    public function __construct(
        #[Autowire]
        protected EventStageRepository $repository,
    ) {
        //
    }

    /**
     * Prepare View.
     *
     * @param  AppContext  $app   The web app context.
     * @param  View        $view  The view object.
     *
     * @return  mixed
     */
    public function prepare(AppContext $app, View $view): array
    {
        $path = $app->input('path');

        $currentCategory = null;

        if ($path) {
            $currentCategory = $this->getCategoryOrFail(compact('path'));
        }

        $page     = $app->input('page');
        $limit    = $app->input('limit') ?? 30;
        $ordering = $this->getDefaultOrdering();

        $now = chronos();

        $items = $this->repository->getAvailableListSelector()
            ->addFilters([])
            ->ordering($ordering)
            ->tapIf(
                (bool) $currentCategory,
                fn (ListSelector $selector) => $selector->where('event.category_id', $currentCategory->getId())
            )
            ->orWhere(
                function (Query $query) use ($now) {
                    $query->where('event_stage.publish_up', null);
                    $query->where('event_stage.publish_up', '<', $now);
                }
            )
            ->orWhere(
                function (Query $query) use ($now) {
                    $query->where('event_stage.end_date', null);
                    $query->where('event_stage.end_date', '>', $now);
                }
            )
            ->page($page)
            ->limit($limit)
            ->setDefaultItemClass(EventStage::class);

        $pagination = $items->getPagination();

        return compact('items', 'pagination', 'currentCategory');
    }

    /**
     * Get default ordering.
     *
     * @return  string
     */
    public function getDefaultOrdering(): string
    {
        return 'event_stage.end_date DESC';
    }

    #[ViewMetadata]
    protected function prepareMetadata(HtmlFrame $htmlFrame): void
    {
        $htmlFrame->setTitle('EventStage List');
    }
}

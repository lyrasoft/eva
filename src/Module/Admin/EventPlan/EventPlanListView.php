<?php

declare(strict_types=1);

namespace App\Module\Admin\EventPlan;

use App\Entity\Event;
use App\Entity\EventPlan;
use App\Entity\EventStage;
use App\Module\Admin\EventPlan\Form\GridForm;
use App\Repository\EventPlanRepository;
use App\Traits\EventScopeViewTrait;
use App\Traits\PriceFormatTrait;
use Unicorn\View\FormAwareViewModelTrait;
use Unicorn\View\ORMAwareViewModelTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewMetadata;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\View\Contract\FilterAwareViewModelInterface;
use Windwalker\Core\View\Traits\FilterAwareViewModelTrait;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\DI\Attributes\Autowire;

/**
 * The EventPlanListView class.
 */
#[ViewModel(
    layout: [
        'default' => 'event-plan-list',
        'modal' => 'event-plan-modal',
    ],
    js: 'event-plan-list.js'
)]
class EventPlanListView implements ViewModelInterface, FilterAwareViewModelInterface
{
    use TranslatorTrait;
    use FilterAwareViewModelTrait;
    use ORMAwareViewModelTrait;
    use FormAwareViewModelTrait;
    use PriceFormatTrait;
    use EventScopeViewTrait;

    public function __construct(
        #[Autowire]
        protected EventPlanRepository $repository,
    ) {
    }

    /**
     * Prepare view data.
     *
     * @param  AppContext  $app   The request app context.
     * @param  View        $view  The view object.
     *
     * @return  array
     */
    public function prepare(AppContext $app, View $view): array
    {
        $state = $this->repository->getState();

        [$event, $eventStage] = $this->prepareCurrentEventAndStage($app, $view);

        // Prepare Items
        $page = $state->rememberFromRequest('page');
        $limit = $state->rememberFromRequest('limit') ?? 30;
        $filter = (array) $state->rememberFromRequest('filter');
        $search = (array) $state->rememberFromRequest('search');
        $ordering = $state->rememberFromRequest('list_ordering') ?? $this->getDefaultOrdering();

        $items = $this->repository->getListSelector()
            ->setFilters($filter)
            ->searchTextFor(
                $search['*'] ?? '',
                $this->getSearchFields()
            )
            ->where('event_plan.stage_id', $eventStage->getId())
            ->ordering($ordering)
            ->page($page)
            ->limit($limit)
            ->setDefaultItemClass(EventPlan::class);

        $pagination = $items->getPagination();

        // Prepare Form
        $form = $this->createForm(GridForm::class)
            ->fill(compact('search', 'filter'));

        $showFilters = $this->isFiltered($filter);

        return compact(
            'items',
            'pagination',
            'form',
            'showFilters',
            'ordering',
            'event',
            'eventStage'
        );
    }

    /**
     * Get default ordering.
     *
     * @return  string
     */
    public function getDefaultOrdering(): string
    {
        return 'event_plan.id DESC';
    }

    /**
     * Get search fields.
     *
     * @return  string[]
     */
    public function getSearchFields(): array
    {
        return [
            'event_plan.id',
            'event_plan.title',
        ];
    }

    #[ViewMetadata]
    protected function prepareMetadata(HtmlFrame $htmlFrame, Event $event, EventStage $eventStage): void
    {
        $htmlFrame->setTitle(
            $this->trans(
                'event.stage.edit.heading',
                event: $event->getTitle(),
                stage: $eventStage->getTitle(),
                title: $this->trans('unicorn.title.grid', title: '票價方案')
            )
        );
    }
}

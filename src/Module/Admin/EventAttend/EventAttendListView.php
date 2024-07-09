<?php

declare(strict_types=1);

namespace App\Module\Admin\EventAttend;

use App\Entity\Event;
use App\Entity\EventAttend;
use App\Entity\EventStage;
use App\Enum\EventOrderState;
use App\Module\Admin\EventAttend\Form\GridForm;
use App\Repository\EventAttendRepository;
use App\Traits\EventScopeViewTrait;
use Unicorn\Selector\ListSelector;
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
 * The EventAttendListView class.
 */
#[ViewModel(
    layout: [
        'default' => 'event-attend-list',
        'modal' => 'event-attend-modal',
    ],
    js: 'event-attend-list.js'
)]
class EventAttendListView implements ViewModelInterface, FilterAwareViewModelInterface
{
    use TranslatorTrait;
    use FilterAwareViewModelTrait;
    use ORMAwareViewModelTrait;
    use FormAwareViewModelTrait;
    use EventScopeViewTrait;

    public function __construct(
        #[Autowire]
        protected EventAttendRepository $repository,
    ) {
    }

    /**
     * Prepare view data.
     *
     * @param  AppContext  $app  The request app context.
     * @param  View        $view  The view object.
     *
     * @return  array
     */
    public function prepare(AppContext $app, View $view): array
    {
        $state = $this->repository->getState();

        $inStage = $app->getMatchedRoute()?->getName() === 'admin::event_stage_attend_list';

        $stageId = $app->input('eventStageId');
        $event = null;
        $eventStage = null;

        if ($stageId) {
            [$event, $eventStage] = $this->prepareCurrentEventAndStage($app, $view);
        }

        // Prepare Items
        $page = $state->rememberFromRequest('page');
        $limit = $state->rememberFromRequest('limit') ?? 30;
        $filter = (array) $state->rememberFromRequest('filter');
        $search = (array) $state->rememberFromRequest('search');
        $ordering = $state->rememberFromRequest('list_ordering') ?? $this->getDefaultOrdering();

        // Clear filters
        if (!($filter['event_attend.event_id'] ?? null)) {
            $filter['event_attend.stage_id'] = '';
        }

        $items = $this->repository->getListSelector()
            ->setFilters($filter)
            ->searchTextFor(
                $search['*'] ?? '',
                $this->getSearchFields()
            )
            ->tapIf(
                (bool) $eventStage,
                fn(ListSelector $selector) => $selector->where('event_attend.stage_id', $eventStage->getEventId())
            )
            ->where('order.state', [EventOrderState::DONE, EventOrderState::PENDING_APPROVAL])
            ->ordering($ordering)
            ->page($page)
            ->limit($limit)
            ->setDefaultItemClass(EventAttend::class);

        $pagination = $items->getPagination();

        // Prepare Form
        $form = $this->createForm(
            GridForm::class,
            inStage: $inStage,
            eventId: (int) ($filter['event_attend.event_id'] ?? null)
        )
            ->fill(compact('search', 'filter'));

        $showFilters = $this->isFiltered($filter);

        return compact(
            'items',
            'pagination',
            'form',
            'showFilters',
            'ordering',
            'eventStage',
            'inStage',
        );
    }

    /**
     * Get default ordering.
     *
     * @return  string
     */
    public function getDefaultOrdering(): string
    {
        return 'event_attend.id DESC';
    }

    /**
     * Get search fields.
     *
     * @return  string[]
     */
    public function getSearchFields(): array
    {
        return [
            'event_attend.id',
            'event_attend.title',
            'event_attend.alias',
        ];
    }

    #[ViewMetadata]
    protected function prepareMetadata(HtmlFrame $htmlFrame, ?Event $event = null, ?EventStage $eventStage = null): void
    {
        $title = $this->trans('unicorn.title.grid', title: '報名者');

        if (!$eventStage) {
            $htmlFrame->setTitle($title);
        } else {
            $htmlFrame->setTitle(
                $this->trans(
                    'event.stage.edit.heading',
                    event: $event?->getTitle(),
                    stage: $eventStage?->getTitle(),
                    title: $title
                )
            );
        }
    }
}

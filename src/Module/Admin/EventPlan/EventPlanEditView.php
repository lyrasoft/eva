<?php

declare(strict_types=1);

namespace App\Module\Admin\EventPlan;

use App\Entity\Event;
use App\Entity\EventPlan;
use App\Entity\EventStage;
use App\Module\Admin\EventPlan\Form\EditForm;
use App\Repository\EventPlanRepository;
use App\Traits\EventScopeViewTrait;
use Unicorn\View\FormAwareViewModelTrait;
use Unicorn\View\ORMAwareViewModelTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewMetadata;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\DI\Attributes\Autowire;

/**
 * The EventPlanEditView class.
 */
#[ViewModel(
    layout: 'event-plan-edit',
    js: 'event-plan-edit.js'
)]
class EventPlanEditView implements ViewModelInterface
{
    use TranslatorTrait;
    use ORMAwareViewModelTrait;
    use FormAwareViewModelTrait;
    use EventScopeViewTrait;

    public function __construct(
        #[Autowire] protected EventPlanRepository $repository,
    ) {
    }

    /**
     * Prepare
     *
     * @param  AppContext  $app
     * @param  View        $view
     *
     * @return  mixed
     */
    public function prepare(AppContext $app, View $view): mixed
    {
        $id = $app->input('id');

        [$event, $eventStage] = $this->prepareCurrentEventAndStage($app, $view);

        /** @var EventPlan $item */
        $item = $this->repository->getItem($id);

        // Bind item for injection
        $view[EventPlan::class] = $item;

        $form = $this->createForm(EditForm::class)
            ->fill(
                [
                    'item' => $this->repository->getState()->getAndForget('edit.data')
                        ?: $this->orm->extractEntity($item)
                ]
            );

        return compact('form', 'id', 'item', 'event', 'eventStage');
    }

    #[ViewMetadata]
    protected function prepareMetadata(HtmlFrame $htmlFrame, Event $event, EventStage $eventStage): void
    {
        $htmlFrame->setTitle(
            $this->trans(
                'event.stage.edit.heading',
                event: $event->getTitle(),
                stage: $eventStage->getTitle(),
                title: $this->trans('unicorn.title.edit', title: '票價方案')
            )
        );
    }
}

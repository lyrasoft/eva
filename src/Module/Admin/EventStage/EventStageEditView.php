<?php

declare(strict_types=1);

namespace App\Module\Admin\EventStage;

use App\Entity\Event;
use App\Entity\EventStage;
use App\Module\Admin\EventStage\Form\EditForm;
use App\Repository\EventStageRepository;
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
 * The EventStageEditView class.
 */
#[ViewModel(
    layout: 'event-stage-edit',
    js: 'event-stage-edit.js'
)]
class EventStageEditView implements ViewModelInterface
{
    use TranslatorTrait;
    use ORMAwareViewModelTrait;
    use FormAwareViewModelTrait;
    use EventScopeViewTrait;

    public function __construct(
        #[Autowire] protected EventStageRepository $repository,
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

        $event = $this->prepareCurrentEvent($app, $view);

        /** @var EventStage $item */
        $item = $this->repository->getItem($id);

        // Bind item for injection
        $view[EventStage::class] = $item;

        $form = $this->createForm(EditForm::class)
            ->fill(
                [
                    'item' => $this->repository->getState()->getAndForget('edit.data')
                        ?: $this->orm->extractEntity($item)
                ]
            );

        $eventStage = $item;

        return compact('form', 'id', 'item', 'event', 'eventStage');
    }

    #[ViewMetadata]
    protected function prepareMetadata(HtmlFrame $htmlFrame, Event $event): void
    {
        $htmlFrame->setTitle(
            $this->trans(
                'event.edit.heading',
                event: $event->getTitle(),
                title: $this->trans('unicorn.title.edit', title: '梯次')
            )
        );
    }
}

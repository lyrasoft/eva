<?php

declare(strict_types=1);

namespace App\Module\Front\EventAttending;

use App\Entity\Event;
use App\Entity\EventStage;
use App\Module\Front\EventAttending\Form\EventAttendingForm;
use App\Service\EventAttendingService;
use App\Service\EventViewService;
use Lyrasoft\Luna\User\UserService;
use Unicorn\View\ORMAwareViewModelTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Form\FormFactory;
use Windwalker\Core\Router\Exception\RouteNotFoundException;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;

#[ViewModel(
    layout: 'event-attending',
    js: 'event-attending.js'
)]
class EventAttendingView implements ViewModelInterface
{
    use ORMAwareViewModelTrait;

    /**
     * Constructor.
     */
    public function __construct(
        protected EventAttendingService $eventAttendingService,
        protected EventViewService $eventViewService,
        protected FormFactory $formFactory,
        protected UserService $userService,
    ) {
        //
    }

    /**
     * Prepare View.
     *
     * @param  AppContext  $app  The web app context.
     * @param  View        $view  The view object.
     *
     * @return  mixed
     */
    public function prepare(AppContext $app, View $view): array
    {
        $stageId = $app->input('stageId');

        $stage = $this->orm->mustFindOne(EventStage::class, $stageId);
        $event = $this->orm->mustFindOne(Event::class, $stage->getEventId());

        [, , $category] = $this->eventViewService->checkEventAndStageAvailable($event, $stage);

        $attendingData = $this->eventAttendingService->getAttendingDataObject($stage);

        $data = $app->state->getAndForget('attending.order.data');

        $user = $this->userService->getUser();

        $form = $this->formFactory->create(EventAttendingForm::class)
            ->fill($data);

        if ($user->isLogin()) {
            $form['order/name']->setValue($user->getName());
            $form['order/email']->setValue($user->getEmail());
        }

        return compact(
            'event',
            'stage',
            'category',
            'attendingData',
            'form',
        );
    }
}

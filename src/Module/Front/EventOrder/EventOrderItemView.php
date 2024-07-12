<?php

declare(strict_types=1);

namespace App\Module\Front\EventOrder;

use App\Entity\Event;
use App\Entity\EventAttend;
use App\Entity\EventOrder;
use App\Entity\EventPlan;
use App\Entity\EventStage;
use App\Repository\EventOrderRepository;
use App\Service\EventPaymentService;
use Lyrasoft\Luna\User\UserService;
use Unicorn\View\ORMAwareViewModelTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewMetadata;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Router\Exception\RouteNotFoundException;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\ORM\ORM;

#[ViewModel(
    layout: [
        'event-order-item',
        'my-event-item'
    ],
    js: [
        'event-order-item' => 'event-order-item.js',
        'my-event-item' => 'event-order-item.js',
    ]
)]
class EventOrderItemView implements ViewModelInterface
{
    use ORMAwareViewModelTrait;

    public function __construct(
        protected UserService $userService,
        protected EventPaymentService $paymentService,
        #[Autowire] protected EventOrderRepository $repository
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
        $no = $app->input('no');

        $user = $this->userService->getUser();

        /** @var EventOrder $item */
        $item = $this->repository->mustGetItem(compact('no'));

        if ($user->getId() !== $item->getUserId()) {
            throw new RouteNotFoundException();
        }

        $attends = $this->orm->from(EventAttend::class)
            ->leftJoin(
                EventPlan::class,
                'plan'
            )
            ->where('event_attend.order_id', $item->getId())
            ->groupByJoins()
            ->all(EventAttend::class);

        $gateway = $this->paymentService->getGateway($item->getPayment());
        $paymentInfo = $gateway?->orderInfo($item, $attends);

        $view[$item::class] = $item;

        return compact('item', 'attends', 'paymentInfo');
    }

    #[ViewMetadata]
    public function prepareMetadata(HtmlFrame $htmlFrame, EventOrder $item): void
    {
        $htmlFrame->setTitle(
            '訂單: ' . $item->getNo()
        );
    }
}

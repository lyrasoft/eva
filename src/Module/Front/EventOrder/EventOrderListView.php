<?php

declare(strict_types=1);

namespace App\Module\Front\EventOrder;

use App\Entity\EventOrder;
use App\Repository\EventOrderRepository;
use Lyrasoft\Luna\User\UserService;
use Unicorn\View\ORMAwareViewModelTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewMetadata;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\DI\Attributes\Autowire;

#[ViewModel(
    layout: 'event-order-list',
    js: 'event-order-list.js'
)]
class EventOrderListView implements ViewModelInterface
{
    use ORMAwareViewModelTrait;

    public function __construct(
        protected UserService $userService,
        #[Autowire]
        protected EventOrderRepository $repository,
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
        $page     = $app->input('page');
        $limit    = $app->input('limit') ?? 30;
        $ordering = $this->getDefaultOrdering();

        $user = $this->userService->getUser();

        $items = $this->repository->getFrontListSelector()
            ->addFilters([])
            ->where('event_order.user_id', $user->getId())
            ->ordering($ordering)
            ->page($page)
            ->limit($limit)
            ->setDefaultItemClass(EventOrder::class);

        $pagination = $items->getPagination();

        return compact('items', 'pagination');
    }

    /**
     * Get default ordering.
     *
     * @return  string
     */
    public function getDefaultOrdering(): string
    {
        return 'event_order.id DESC';
    }

    #[ViewMetadata]
    protected function prepareMetadata(HtmlFrame $htmlFrame): void
    {
        $htmlFrame->setTitle('EventOrder List');
    }
}

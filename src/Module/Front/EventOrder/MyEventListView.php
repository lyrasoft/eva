<?php

declare(strict_types=1);

namespace App\Module\Front\EventOrder;

use App\Entity\EventOrder;
use App\Enum\EventOrderState;
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
    layout: 'my-event-list',
    js: 'my-event-list.js'
)]
class MyEventListView implements ViewModelInterface
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
            ->where(
                'event_order.state',
                EventOrderState::DONE
            )
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
        return 'stage.start_date DESC, stage.end_date DESC';
    }

    #[ViewMetadata]
    protected function prepareMetadata(HtmlFrame $htmlFrame): void
    {
        $htmlFrame->setTitle('MyEvent List');
    }
}

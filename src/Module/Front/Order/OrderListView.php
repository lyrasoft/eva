<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Module\Front\Order;

use Lyrasoft\Luna\User\UserService;
use Lyrasoft\ShopGo\Module\Admin\Order\Form\GridForm;
use Lyrasoft\ShopGo\Repository\OrderRepository;
use Lyrasoft\ShopGo\Traits\CurrencyAwareTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\Data\Collection;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\ORM\ORM;

/**
 * The OrderListView class.
 */
#[ViewModel(
    layout: 'order-list',
    js: 'order-list.js'
)]
class OrderListView implements ViewModelInterface
{
    use TranslatorTrait;
    use CurrencyAwareTrait;

    /**
     * Constructor.
     */
    public function __construct(
        protected ORM $orm,
        #[Autowire]
        protected OrderRepository $repository,
        protected UserService $userService,
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
        // Prepare Items
        $page = $app->input('page');

        $user = $this->userService->getUser();

        $items = $this->repository->getFrontListSelector($user)
            ->ordering('order.id', 'DESC')
            ->page($page)
            ->limit(15);

        $pagination = $items->getPagination();

        $this->prepareMetadata($app, $view);

        return compact('items', 'pagination');
    }

    public function prepareItem(Collection $item): object
    {
        return $this->repository->getEntityMapper()->toEntity($item);
    }

    protected function prepareMetadata(AppContext $app, View $view)
    {
        //
    }
}

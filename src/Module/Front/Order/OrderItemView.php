<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Module\Front\Order;

use Lyrasoft\Luna\Entity\User;
use Lyrasoft\Luna\User\UserService;
use Lyrasoft\ShopGo\Cart\Price\PriceObject;
use Lyrasoft\ShopGo\Cart\Price\PriceSet;
use Lyrasoft\ShopGo\Entity\Order;
use Lyrasoft\ShopGo\Entity\OrderHistory;
use Lyrasoft\ShopGo\Entity\OrderItem;
use Lyrasoft\ShopGo\Entity\OrderTotal;
use Lyrasoft\ShopGo\Repository\OrderRepository;
use Lyrasoft\ShopGo\Traits\CurrencyAwareTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Form\Exception\ValidateFailException;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\Data\Collection;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\Cache\InstanceCacheTrait;

/**
 * The OrderItemView class.
 */
#[ViewModel(
    layout: 'order-item',
    js: 'order-item.js'
)]
class OrderItemView implements ViewModelInterface
{
    use InstanceCacheTrait;
    use CurrencyAwareTrait;
    use TranslatorTrait;

    /**
     * Constructor.
     */
    public function __construct(
        protected ORM $orm,
        protected Navigator $nav,
        protected UserService $userService,
        #[Autowire] protected OrderRepository $repository
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
     * @throws \ReflectionException
     */
    public function prepare(AppContext $app, View $view): mixed
    {
        $no = $app->input('no');

        /** @var Order $item */
        $item = $this->repository->getItem(compact('no'));

        $user = $this->userService->getUser();

        if ($item->getUserId() !== $user->getId()) {
            throw new ValidateFailException();
        }

        // Totals
        $totalItems = $this->orm->mapper(OrderTotal::class)
            ->select()
            ->where('order_id', $item->getId())
            ->order('ordering', 'ASC')
            ->all(OrderTotal::class)
            ->map(
                function (OrderTotal $total) {
                    return PriceObject::create(
                        $total->getCode(),
                        (string) $total->getValue(),
                        $total->getTitle()
                    )
                        ->widthParams($total->dump());
                }
            );

        $totals = new PriceSet();

        foreach ($totalItems as $totalItem) {
            $totals->set($totalItem);
        }

        $histories = $this->getOrderHistories($item);

        $orderItems = $this->orm->findList(
            OrderItem::class,
            [
                'order_id' => $item->getId(),
            ]
        )
            ->all();

        $this->prepareMetadata($app, $view);

        return compact('no', 'item', 'orderItems', 'totals', 'histories');
    }

    /**
     * Prepare Metadata and HTML Frame.
     *
     * @param  AppContext  $app
     * @param  View        $view
     *
     * @return  void
     */
    protected function prepareMetadata(AppContext $app, View $view): void
    {
        $view->getHtmlFrame()
            ->setTitle(
                $this->trans('unicorn.title.edit', title: $this->trans('shopgo.order.title'))
            );
    }

    public function getOrderHistories(Order $order): Collection
    {
        return $this->cacheStorage['histories.' . $order->getId()]
            ??= $this->orm
            ->from(OrderHistory::class)
            ->leftJoin(User::class, 'user', 'user.id', 'order_history.created_by')
            ->where('order_history.order_id', $order->getId())
            ->order('order_history.id', 'DESC')
            ->groupByJoins()
            ->all(OrderHistory::class);
    }
}

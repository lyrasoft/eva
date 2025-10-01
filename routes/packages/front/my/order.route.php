<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Front\Order\OrderController;
use Lyrasoft\ShopGo\Module\Front\Order\OrderItemView;
use Lyrasoft\ShopGo\Module\Front\Order\OrderListView;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('order')
    ->register(function (RouteCreator $router) {
        $router->any('my_order_list', '/order/list')
            ->alias('order_list')
            ->controller(OrderController::class)
            ->view(OrderListView::class);

        $router->any('my_order_item', '/order/item/{no}')
            ->alias('order_item')
            ->controller(OrderController::class)
            ->view(OrderItemView::class);
    });

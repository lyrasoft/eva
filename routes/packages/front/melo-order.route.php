<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Melo\Module\Front\Order\OrderController;
use Lyrasoft\Melo\Module\Front\Order\OrderItemView;
use Lyrasoft\Melo\Module\Front\Order\OrderListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('melo_order')
    ->extra('menu', ['sidemenu' => 'melo_order_list'])
    ->register(function (RouteCreator $router) {
        $router->any('melo_order_list', '/lesson/order/list')
            ->controller(OrderController::class)
            ->view(OrderListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('melo_order_item', '/lesson/order[/{no}]')
            ->controller(OrderController::class)
            ->view(OrderItemView::class);
    });

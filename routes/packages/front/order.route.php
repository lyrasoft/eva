<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Melo\Module\Front\Order\OrderController;
use Lyrasoft\Melo\Module\Front\Order\OrderItemView;
use Lyrasoft\Melo\Module\Front\Order\OrderListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('order')
    ->extra('menu', ['sidemenu' => 'order_list'])
    ->register(function (RouteCreator $router) {
        $router->any('order_list', '/order/list')
            ->controller(OrderController::class)
            ->view(OrderListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('order_item', '/order[/{id}]')
            ->controller(OrderController::class)
            ->view(OrderItemView::class);
    });

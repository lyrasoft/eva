<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Melo\Module\Admin\Order\OrderController;
use Lyrasoft\Melo\Module\Admin\Order\OrderEditView;
use Lyrasoft\Melo\Module\Admin\Order\OrderListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('order')
    ->extra('menu', ['sidemenu' => 'order_list'])
    ->register(function (RouteCreator $router) {
        $router->any('melo_order_list', '/melo/order/list')
            ->controller(OrderController::class)
            ->view(OrderListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('melo_order_edit', '/melo/order/edit[/{id}]')
            ->controller(OrderController::class)
            ->view(OrderEditView::class);
    });

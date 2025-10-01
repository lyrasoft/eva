<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Admin\OrderState\OrderStateController;
use Lyrasoft\ShopGo\Module\Admin\OrderState\OrderStateEditView;
use Lyrasoft\ShopGo\Module\Admin\OrderState\OrderStateListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('order-state')
    ->extra('menu', ['sidemenu' => 'order_state_list'])
    ->register(function (RouteCreator $router) {
        $router->any('order_state_list', '/order-state/list')
            ->controller(OrderStateController::class)
            ->view(OrderStateListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('order_state_edit', '/order-state/edit[/{id}]')
            ->controller(OrderStateController::class)
            ->view(OrderStateEditView::class);
    });

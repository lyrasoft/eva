<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\EventBooking\Module\Front\EventOrder\EventOrderController;
use Lyrasoft\EventBooking\Module\Front\EventOrder\EventOrderItemView;
use Lyrasoft\EventBooking\Module\Front\EventOrder\EventOrderListView;
use Lyrasoft\EventBooking\Module\Front\EventOrder\MyEventListView;
use Lyrasoft\Luna\Middleware\LoginRequireMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('event-order')
    ->extra('menu', ['sidemenu' => 'event_order_list'])
    ->middleware(LoginRequireMiddleware::class)
    ->register(function (RouteCreator $router) {
        $router->any('event_order_list', '/my/event/orders')
            ->controller(EventOrderController::class)
            ->view(EventOrderListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('event_order_item', '/my/event/order/{no}')
            ->controller(EventOrderController::class)
            ->view(EventOrderItemView::class)
            ->var('layout', 'event-order-item');

        $router->any('my_event_list', '/my/events')
            ->view(MyEventListView::class)
            ->putHandler('filter');

        $router->any('my_event_item', '/my/event/{no}')
            ->view(EventOrderItemView::class)
            ->var('layout', 'my-event-item');
    });

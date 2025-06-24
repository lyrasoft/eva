<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\EventBooking\Module\Admin\EventOrder\EventOrderController;
use Lyrasoft\EventBooking\Module\Admin\EventOrder\EventOrderEditView;
use Lyrasoft\EventBooking\Module\Admin\EventOrder\EventOrderListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('event-order')
    ->extra('menu', ['sidemenu' => 'event_order_list'])
    ->register(function (RouteCreator $router) {
        $router->any('event_order_list', '/event/order/list')
            ->controller(EventOrderController::class)
            ->view(EventOrderListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('event_order_edit', '/event/order/edit[/{id}]')
            ->controller(EventOrderController::class)
            ->view(EventOrderEditView::class);
    });

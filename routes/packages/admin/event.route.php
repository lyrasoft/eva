<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\EventBooking\Module\Admin\Event\EventController;
use Lyrasoft\EventBooking\Module\Admin\Event\EventEditView;
use Lyrasoft\EventBooking\Module\Admin\Event\EventListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('event')
    ->extra('menu', ['sidemenu' => 'event_list'])
    ->register(function (RouteCreator $router) {
        $router->any('event_list', '/event/list')
            ->controller(EventController::class)
            ->view(EventListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('event_edit', '/event/edit[/{id}]')
            ->controller(EventController::class)
            ->view(EventEditView::class);
    });

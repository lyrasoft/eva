<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Front\EventAttending\EventAttendingController;
use App\Module\Front\EventAttending\EventAttendingView;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('event-attending')
    ->register(function (RouteCreator $router) {
        $router->any('event_attending', '/event/attending/{stageId}')
            ->controller(EventAttendingController::class)
            ->view(EventAttendingView::class)
            ->postHandler('attending');

        $router->any('event_checkout', '/event/checkout/{stageId}')
            ->controller(EventAttendingController::class, 'checkout');

        $router->any('event_receive_notify', '/event/receive/notify/{id}')
            ->controller(EventAttendingController::class, 'receiveNotify');
    });

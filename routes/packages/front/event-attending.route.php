<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\EventBooking\Module\Front\EventAttending\EventAttendingController;
use Lyrasoft\EventBooking\Module\Front\EventAttending\EventAttendingView;
use Lyrasoft\Luna\Middleware\LoginRequireMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('event-attending')
    ->register(function (RouteCreator $router) {
        $router->post('event_attending_save', '/event/attending/save/{stageId}')
            ->controller(EventAttendingController::class, 'attending');

        $router->get('event_attending', '/event/attending/{stageId}')
            ->middleware(LoginRequireMiddleware::class)
            ->view(EventAttendingView::class);

        $router->any('event_checkout', '/event/checkout/{stageId}')
            ->middleware(LoginRequireMiddleware::class)
            ->controller(EventAttendingController::class, 'checkout');

        $router->any('event_payment_task', '/event/payment/task/{id}')
            ->controller(EventAttendingController::class, 'paymentTask');
    });

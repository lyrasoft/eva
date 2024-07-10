<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Admin\EventAttend\EventAttendController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('event-attending')
    ->register(function (RouteCreator $router) {
        $router->any('event_attending', '/event/attending')
            ->controller(EventAttendController::class, 'attending');

        $router->any('event_checkout', '/event/checkout')
            ->controller(EventAttendController::class, 'checkout');
    });

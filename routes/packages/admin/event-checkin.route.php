<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\EventBooking\Module\Admin\EventCheckin\EventCheckinController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('event-checkin')
    ->register(function (RouteCreator $router) {
        $router->any('event_checkin', '/event/checkin/{attendNo}')
            ->controller(EventCheckinController::class, 'checkin');
    });

<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\EventBooking\Module\Admin\Venue\VenueController;
use Lyrasoft\EventBooking\Module\Admin\Venue\VenueEditView;
use Lyrasoft\EventBooking\Module\Admin\Venue\VenueListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('venue')
    ->extra('menu', ['sidemenu' => 'venue_list'])
    ->register(function (RouteCreator $router) {
        $router->any('venue_list', '/venue/list')
            ->controller(VenueController::class)
            ->view(VenueListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('venue_edit', '/venue/edit[/{id}]')
            ->controller(VenueController::class)
            ->view(VenueEditView::class);
    });

<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\EventBooking\Module\Admin\EventAttend\EventAttendController;
use Lyrasoft\EventBooking\Module\Admin\EventAttend\EventAttendEditView;
use Lyrasoft\EventBooking\Module\Admin\EventAttend\EventAttendListView;
use Unicorn\Middleware\KeepUrlQueryMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('event-attend')
    ->register(function (RouteCreator $router) {
        $router->any('event_attend_list', '/event/attend/list')
            ->controller(EventAttendController::class)
            ->view(EventAttendListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        // $router->any('event_attend_edit', '/event/attend/edit[/{id}]')
        //     ->controller(EventAttendController::class)
        //     ->view(EventAttendEditView::class);
    });

$router->group('event-stage-attend')
    ->middleware(
        KeepUrlQueryMiddleware::class,
        options: [
            'key' => 'eventId',
            'uid' => 'event_edit',
        ]
    )
    ->middleware(
        KeepUrlQueryMiddleware::class,
        options: [
            'key' => 'eventStageId',
            'uid' => 'event_stage_edit',
        ]
    )
    ->register(function (RouteCreator $router) {
        $router->any('event_stage_attend_list', '/event/{eventId}/stage/{eventStageId}/attend/list')
            ->controller(EventAttendController::class)
            ->view(EventAttendListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('event_attend_edit', '/event/attend/edit[/{id}]')
            ->controller(EventAttendController::class)
            ->view(EventAttendEditView::class);
    });

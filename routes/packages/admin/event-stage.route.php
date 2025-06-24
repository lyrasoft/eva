<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\EventBooking\Module\Admin\EventStage\EventStageController;
use Lyrasoft\EventBooking\Module\Admin\EventStage\EventStageEditView;
use Lyrasoft\EventBooking\Module\Admin\EventStage\EventStageListView;
use Unicorn\Middleware\KeepUrlQueryMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('event-stage')
    ->extra('menu', ['sidemenu' => 'event_stage_list'])
    ->middleware(
        KeepUrlQueryMiddleware::class,
        options: [
            'key' => 'eventId',
            'uid' => 'event_edit'
        ]
    )
    ->register(function (RouteCreator $router) {
        $router->any('event_stage_list', '/event/{eventId}/stage/list')
            ->controller(EventStageController::class)
            ->view(EventStageListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('event_stage_edit', '/event/{eventId}/stage/edit[/{id}]')
            ->controller(EventStageController::class)
            ->view(EventStageEditView::class);
    });

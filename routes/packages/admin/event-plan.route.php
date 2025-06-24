<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\EventBooking\Module\Admin\EventPlan\EventPlanController;
use Lyrasoft\EventBooking\Module\Admin\EventPlan\EventPlanEditView;
use Lyrasoft\EventBooking\Module\Admin\EventPlan\EventPlanListView;
use Unicorn\Middleware\KeepUrlQueryMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('event-plan')
    ->extra('menu', ['sidemenu' => 'event_plan_list'])
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
        $router->any('event_plan_list', '/event/{eventId}/stage/{eventStageId}/plan/list')
            ->controller(EventPlanController::class)
            ->view(EventPlanListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('event_plan_edit', '/event/{eventId}/stage/{eventStageId}/edit[/{id}]')
            ->controller(EventPlanController::class)
            ->view(EventPlanEditView::class);
    });

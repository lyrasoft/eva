<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\EventBooking\Module\Front\EventStage\EventStageController;
use Lyrasoft\EventBooking\Module\Front\EventStage\EventStageItemView;
use Lyrasoft\EventBooking\Module\Front\EventStage\EventStageListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('event-stage')
    ->extra('menu', ['sidemenu' => 'event_stage_list'])
    ->register(function (RouteCreator $router) {
        $router->any('event_stage_list', '/event/stages[/{path:.+}]')
            ->controller(EventStageController::class)
            ->view(EventStageListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('event_stage_item', '/event/stage/{id:\d+}[-{alias}]')
            ->controller(EventStageController::class)
            ->view(EventStageItemView::class);
    });

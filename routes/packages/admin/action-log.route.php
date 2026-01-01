<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ActionLog\Module\Admin\ActionLog\ActionLogController;
use Lyrasoft\ActionLog\Module\Admin\ActionLog\ActionLogEditView;
use Lyrasoft\ActionLog\Module\Admin\ActionLog\ActionLogListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('action-log')
    ->extra('menu', ['sidemenu' => 'action_log_list'])
    ->register(function (RouteCreator $router) {
        $router->any('action_log_list', '/action-log/list')
            ->controller(ActionLogController::class)
            ->view(ActionLogListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('action_log_edit', '/action-log/edit[/{id}]')
            ->controller(ActionLogController::class)
            ->view(ActionLogEditView::class);

        $router->any('action_log_export', '/action-log/export')
            ->controller(ActionLogController::class, 'export');
    });

<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Admin\Location\LocationController;
use Lyrasoft\ShopGo\Module\Admin\Location\LocationEditView;
use Lyrasoft\ShopGo\Module\Admin\Location\LocationListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('location')
    ->extra('menu', ['sidemenu' => 'location_list'])
    ->register(function (RouteCreator $router) {
        $router->any('location_list', '/location/list')
            ->controller(LocationController::class)
            ->view(LocationListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('location_edit', '/location/edit[/{id}]')
            ->controller(LocationController::class)
            ->view(LocationEditView::class);

        $router->any('location_ajax', '/location/ajax[/{task}]')
            ->controller(LocationController::class, 'ajax');
    });

<?php

namespace App\Routes;

use App\Module\Admin\Portfolio\PortfolioController;
use App\Module\Admin\Portfolio\PortfolioEditView;
use App\Module\Admin\Portfolio\PortfolioListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('portfolio')
    ->register(function (RouteCreator $router) {
        $router->any('portfolio_list', '/portfolio/list')
            ->controller(PortfolioController::class)
            ->view(PortfolioListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('portfolio_edit', '/portfolio/edit[/{id}]')
            ->controller(PortfolioController::class)
            ->view(PortfolioEditView::class);
    });
